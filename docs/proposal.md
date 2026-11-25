# Project Proposal: AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench

**Course:** ZUMS ITM 427 (Software Engineering) / ITM 428 (Software Engineering Lab)  
**Student:** Moktadir  

---

## 1. Title
**AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench**

## 2. Description
An enterprise-style Software Engineering workbench built with Laravel MVC that enables teams to ingest knowledge documents, test interchangeable chunking and retrieval algorithms, version prompt templates with dynamic `{{variables}}`, execute side-by-side RAG evaluations, and track automated grounding/completeness quality scores.

## 3. System Overview
Modern LLM applications suffer from prompt regressions, poor document chunking boundaries, and unverified hallucinations. This workbench solves these challenges by treating prompts and knowledge bases as versioned, testable software artifacts:
- **Workspaces:** Multi-workspace organization with role metadata and 1-click guest demo access.
- **Knowledge Base & Chunking:** Upload or paste documents, inspect generated chunks, and switch chunking strategies.
- **Prompt Studio:** Edit system/user templates with `{{variable}}` detection and immutable Memento snapshots.
- **Side-by-Side Test Runner:** Compare multiple prompt versions or RAG configurations on identical queries with latency, token, and rubric metrics.

## 4. Technology Used
- **Backend:** PHP 8.3+, Laravel Framework (Strict MVC + Service/Repository layers)
- **Database:** MySQL 8.0+ (Local) & Neon Cloud PostgreSQL (Live Demo)
- **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js
- **AI / Fallback:** Google Gemini API (`GEMINI_API_KEY`) + `DeterministicMockProvider` offline fallback
- **Testing & VCS:** PHPUnit 12, Git (`main`, `dev`, `feature/*`)

## 5. Architecture & Design Patterns
- **Strict MVC Separation:** Models (`Workspace`, `Document`, `DocumentChunk`, `PromptTemplate`, `PromptVersion`, `TestRun`, `TestRunResult`, `EvaluationScore`, `RefactorLog`), FormRequests, Controllers, Repositories, and Blade Views.
- **Facade Pattern (`EvaluationFacade`):** Unifies chunking, retrieval, prompt interpolation, LLM/fallback execution, and scoring.
- **Strategy Pattern:** Interchangeable algorithms for Chunking, Retrieval/Ranking, and Evaluation Rubrics.
- **Memento Pattern (`MementoService`):** Immutable `PromptVersion` and `DocumentVersion` snapshots supporting rollback.

## 6. Workflow
1. Create or open a Workspace (or click **1-Click Evaluator Demo**).
2. Ingest a Document and select a **Chunking Strategy** (`Fixed-Size` or `Paragraph-Boundary`).
3. Create/edit a **Prompt Template**; each save records a **Memento** snapshot.
4. Execute a **TestRun** selecting 1–4 Prompt Versions, a **Retrieval Strategy**, and an **Evaluation Rubric**.
5. Inspect the **Side-by-Side Comparison** (outputs, latency, tokens, grounding/completeness scores, pass/fail regression status).
