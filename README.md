# ARIA-path

ARIA-path (Agentic Retrieval-augmented Interpretation and Annotation for pathology) is a pathology platform with two main parts:

- `ARIA-pathWeb`: Laravel web application for user management, image browsing, and annotation workflows.
- `ARIA-pathAPI`: FastAPI backend services for AI copilot, retrieval/agent logic, and image-processing APIs.

Website: https://ai.swmed.edu/ARIA-path/

## Repository Layout

```text
ARIA-path/
├── ARIA-pathWeb/   # Laravel website
└── ARIA-pathAPI/   # FastAPI backend and AI services
```

## Quick Start

1. Set up backend first: see `ARIA-pathAPI/README.md`.
2. Set up web app next: see `ARIA-pathWeb/README.md`.
3. Configure integration values (hosts/ports) so Web can call API services.

## License

The software package is [licensed](https://github.com/QBRC/ARIA-path/blob/master/LICENSE). 
For commercial use, please contact [Ruichen Rong](Ruichen.Rong@UTSouthwestern.edu), [Danni Luo](Danni.Luo@UTSouthwestern.edu), [Xiaowei Zhan](mailto:Xiaowei.Zhan@UTSouthwestern.edu) and
[Guanghua Xiao](mailto:Guanghua.Xiao@UTSouthwestern.edu).
