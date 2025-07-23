import os
import sqlite3
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


class MessageRequest(BaseModel):
    creator_id: str
    message: str


@app.post("/api/v1/message")
async def handle_message(req: MessageRequest):
    # store user message
    conn = get_db()
    conn.execute(
        "INSERT INTO history (creator_id, role, message) VALUES (?,?,?)",
        (req.creator_id, "user", req.message),
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
        query_texts=[req.message], n_results=3, where={"creator_id": req.creator_id}
    )
    facts = ""
    if results.get("documents"):
        docs = results["documents"][0]
        if docs:
            facts = "\n".join(docs)

    prompt = (
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
    conn.close()

    return {"response": response_text}
