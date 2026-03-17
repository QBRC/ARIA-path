# ARIA-pathAPI

FastAPI backend services for ARIA-path (copilot, agents, and image-processing APIs).


## Prerequisites

- Docker and Docker Compose
- Optional LLM provider:
- `ollama` for local models, or
- OpenAI API key for hosted models

## Clone and Enter Project

```bash
git clone https://github.com/QBRC/ARIA-path.git
cd ARIA-path/ARIA-pathAPI
```

## Environment Configuration

Create `.env` in the project root.

```dotenv
SLIDES_DIR=input_slides_folder
DATABASE_DIR=./databases

OLLAMA_HOST_LLM=localhost
OLLAMA_PORT_LLM=11434
OLLAMA_HOST_MLLM=localhost
OLLAMA_PORT_MLLM=11435

OPENAI_API_KEY=your_openai_api_key

SEGMENT_HOST=localhost
SEGMENT_PORT=8376
```

Notes:
- Set `OPENAI_API_KEY` if using OpenAI.
- Set `OLLAMA_*` values if using local Ollama services.

## Optional: Start Ollama

For image captioning model:

```bash
export OLLAMA_HOST="0.0.0.0:11434"
export OLLAMA_ORIGINS="*"
ollama pull llava
ollama serve
```

For copilot LLM model:

```bash
export OLLAMA_HOST="0.0.0.0:11435"
export OLLAMA_ORIGINS="*"
ollama pull llama3
ollama serve
```

## Run Backend (Docker)

```bash
docker compose up -d
```

## Health Check

After startup, check backend health endpoint (service/port depends on your compose mapping).

## Optional: Segmentation Worker (GPU)

If using SAM2 worker pipeline:

```bash
export MODEL_REGISTRY=sam2-b
export REDIS_HOST=localhost
export REDIS_PORT=6379
nohup celery -A app_worker.celery worker --loglevel=info -Q sam2-b > z.sam2_b.log &
python app_queue.py
```

Then you can connect the backend API with frontend server. Or view a demo by opening the `./templates/index.html`

## Integration

Once API is running, configure ARIA-pathWeb to call the backend host/port values in your environment settings.

## Adding Agents to I-Viewer Copilot
User can integrate additional agent into the RAG system through function registration. The function registration requires the following parameters:
1. name: the name of this agent, must be unique.
2. type: the type of this agent, one of `FunctionTool` or `QueryEngineTool` or None. (More llama_index tool will be supported in future)
3. input_mapping: this will map function args with RAG memory. 
4. output_mapping: this will map the agent output to the RAG memory, results in RAG memory can be further used by other functions for chain of thoughts and hierarchical agent calling.
5. description: the detail description about the agent, what can the agent do, what types of results the agent can provide. The RAG system will heavily rely on the description to determine answering logic. 
6. return_direct: whether to directly return function results, or wrap the results into language template.

The following example registered a basic function to summarize nuclei composition in annotation databases. In the I-Viewer, `image`, `annotations`, `roi`, `description` information will be auto-loaded when the copilot window start.
```
## Basic nuclei summary function
description = f"""\
    Summarize the nuclei information from a given dataframe. \
    This tool calculates the statistical summary of nuclei for each different type. 
    Use this tool to answer user questions about percentage, count about nuclei.
"""
@register_agent(
    name='nuclei_composition',
    type='FunctionTool',
    input_mapping={'entries': 'annotations',},
    output_mapping='nuclei_composition_statistics',
    description=description,
    return_direct=False,
)
def nuclei_composition_summary(entries):
    df = pd.DataFrame(entries)
    if not df.empty:
        res = df['label'].value_counts().to_dict()
    else:
        res = {}
    
    return res
```