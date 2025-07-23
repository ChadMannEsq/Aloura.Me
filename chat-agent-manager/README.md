# Chat Agent Manager

This service exposes a simple FastAPI application that connects to a local [Ollama](https://ollama.ai/) LLM instance. It stores recent conversation history in SQLite and long‑term facts in a Chroma vector database.

## Requirements

- Python 3.10+
- Local Ollama server running (usually listening at `http://localhost:11434`)
- GPU drivers configured for Ollama models

Install dependencies:

```bash
pip install -r requirements.txt
```

## Running

Start the API with:

```bash
uvicorn main:app --reload
```

Set `OLLAMA_MODEL` to override the model name (defaults to `llama2`).

The main endpoint accepts a JSON payload with a `message` and `creator_id`:

```bash
curl -X POST http://localhost:8000/api/v1/message \
     -H 'Content-Type: application/json' \
     -d '{"creator_id": "user1", "message": "Hello"}'
```

It returns the AI response after retrieving recent conversation history and user facts stored in Chroma.
