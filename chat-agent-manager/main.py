import os
import sqlite3
import json
from uuid import uuid4
from fastapi import FastAPI
from pydantic import BaseModel
import httpx
import chromadb
from chromadb.config import Settings

DB_DIR = os.path.join(os.path.dirname(__file__), 'data')
DB_PATH = os.path.join(DB_DIR, 'chat.db')
CHROMA_DIR = os.path.join(DB_DIR, 'chroma')

app = FastAPI(title="Chat Agent Manager")

# Initialize database and chroma on startup
@app.on_event("startup")
async def startup_event():
    os.makedirs(DB_DIR, exist_ok=True)
    conn = sqlite3.connect(DB_PATH)
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            creator_id TEXT,
            role TEXT,
            message TEXT,
            ts DATETIME DEFAULT CURRENT_TIMESTAMP
        )
        """
    )
    conn.execute(
        """
        CREATE TABLE IF NOT EXISTS user_traits (
            creator_id TEXT PRIMARY KEY,
            traits TEXT,
            last_summary_ts DATETIME
        )
        """
    )
    conn.commit()
    conn.close()
    os.makedirs(CHROMA_DIR, exist_ok=True)


def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def get_collection():
    client = chromadb.Client(
        Settings(chroma_db_impl="duckdb+parquet", persist_directory=CHROMA_DIR)
    )
    return client.get_or_create_collection("user_facts")


async def summarise_old_messages(creator_id: str, conn, collection):
    rows = conn.execute(
        "SELECT id, role, message FROM history WHERE creator_id=? ORDER BY id",
        (creator_id,),
    ).fetchall()
    if len(rows) <= 20:
        return
    to_sum = rows[:-10]
    text = "\n".join(f"{r['role']}: {r['message']}" for r in to_sum)
    model = os.getenv("OLLAMA_MODEL", "llama2")
    async with httpx.AsyncClient() as client:
        r = await client.post(
            "http://localhost:11434/api/generate",
            json={
                "model": model,
                "prompt": f"Summarise the following conversation highlighting favourite topics:\n{text}\nSummary:",
            },
        )
        r.raise_for_status()
        summary = r.json().get("response", "")
    if summary:
        collection.add_texts(
            [summary],
            ids=[str(uuid4())],
            metadatas=[{"creator_id": creator_id, "type": "summary"}],
        )
        collection.persist()
        last_id = to_sum[-1]["id"]
        conn.execute(
            "DELETE FROM history WHERE creator_id=? AND id<=?", (creator_id, last_id)
        )
        conn.commit()


class MessageRequest(BaseModel):
    creator_id: str
    message: str
    traits: dict | None = None


@app.post("/api/v1/message")
async def handle_message(req: MessageRequest):
    # store user message
    conn = get_db()
    conn.execute(
        "INSERT INTO history (creator_id, role, message) VALUES (?,?,?)",
        (req.creator_id, "user", req.message),
    )
    conn.commit()

    if req.traits:
        row = conn.execute(
            "SELECT traits FROM user_traits WHERE creator_id=?",
            (req.creator_id,),
        ).fetchone()
        data = {}
        if row and row[0]:
            try:
                data = json.loads(row[0])
            except Exception:
                data = {}
        data.update(req.traits)
        conn.execute(
            "INSERT OR REPLACE INTO user_traits (creator_id, traits, last_summary_ts) VALUES (?, ?, COALESCE((SELECT last_summary_ts FROM user_traits WHERE creator_id=?), CURRENT_TIMESTAMP))",
            (req.creator_id, json.dumps(data), req.creator_id),
        )
        conn.commit()

    # add to vector store
    collection = get_collection()
    collection.add_texts(
        [req.message],
        ids=[str(uuid4())],
        metadatas=[{"creator_id": req.creator_id}],
    )
    collection.persist()

    # fetch recent history
    rows = conn.execute(
        "SELECT role, message FROM history WHERE creator_id=? ORDER BY id DESC LIMIT 5",
        (req.creator_id,),
    ).fetchall()
    history = "\n".join(
        f"{row['role']}: {row['message']}" for row in reversed(rows)
    )
    conn.close()

    # fetch user facts
    results = collection.query(
        query_texts=[req.message], n_results=5, where={"creator_id": req.creator_id}
    )
    facts = ""
    if results.get("documents"):
        docs = results["documents"][0]
        if docs:
            facts = "\n".join(docs)

    conn = get_db()
    row = conn.execute(
        "SELECT traits FROM user_traits WHERE creator_id=?", (req.creator_id,)
    ).fetchone()
    traits_txt = row[0] if row and row[0] else ""
    conn.close()

    prompt = (
        f"Segmentation tags:\n{traits_txt}\n"
        f"User facts:\n{facts}\n"
        f"Conversation history:\n{history}\n"
        f"User: {req.message}\nAssistant:"
    )

    model = os.getenv("OLLAMA_MODEL", "llama2")
    async with httpx.AsyncClient() as client:
        r = await client.post(
            "http://localhost:11434/api/generate",
            json={"model": model, "prompt": prompt},
        )
        r.raise_for_status()
        data = r.json()
        response_text = data.get("response", "")

    # store assistant response
    conn = get_db()
    conn.execute(
        "INSERT INTO history (creator_id, role, message) VALUES (?,?,?)",
        (req.creator_id, "assistant", response_text),
    )
    conn.commit()
    await summarise_old_messages(req.creator_id, conn, collection)
    conn.close()

    return {"response": response_text}
