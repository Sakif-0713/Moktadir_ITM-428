# Final Submission Report: AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench

**Course:** ZUMS ITM 427 / ITM 428 Lab  
**Student:** Moktadir  
**Live Hosted Demo URL:** `<INSERT_LIVE_HOSTED_URL>` *(Use "1-Click Evaluator Demo" on `/` for instant access)*  
**GitHub Repository URL:** https://github.com/Sakif-0713/Moktadir_ITM-428  
**Standalone SQL File:** `database.sql` *(uploaded separately alongside `.docx`)*

---

## 1. Requirements Traceability Matrix (CLO1–CLO4)

| CLO | Requirement | Implemented Component(s) | Verification |
|---|---|---|---|
| **CLO1** | Requirements Engineering & Estimation | `docs/proposal.md`, Story Point / FP breakdown | Approved plan & Section 4 |
| **CLO2** | OOP & Design Patterns | `EvaluationFacade`, `Chunking/Retrieval/Rubric` Strategies, `MementoService` | Unit & Integration Tests |
| **CLO3** | MVC & Clean Architecture | `app/Models`, `app/Repositories`, `app/Http/Controllers`, `resources/views` | `GuestDemoSmokeTest` |
| **CLO4** | QA Testing, Risk & Technical Docs | `tests/Unit`, `tests/Feature`, `RefactorLog`, `README.md`, `database.sql` | `php artisan test` (14/14 pass) |

---

## 2. MVC & Design Patterns Breakdown
1. **Facade Pattern (`app/Services/Facades/EvaluationFacade.php`):**
   - Exposes `ingestDocumentChunks()` and `executeTestRun()`.
   - Hides strategy resolution, `{{variable}}` interpolation, `GeminiProvider` HTTP calls, automatic fallback to `DeterministicMockProvider`, latency/token telemetry, and rubric scoring.
2. **Strategy Pattern (`app/Services/RAG/*`):**
   - **Chunking:** `FixedSizeChunkingStrategy` vs. `ParagraphBoundaryChunkingStrategy`.
   - **Retrieval:** `LexicalOverlapRetrievalStrategy` vs. `HybridSimilarityRetrievalStrategy`.
   - **Evaluation Rubric:** `GroundingFaithfulnessStrategy` vs. `CompletenessStructureStrategy`.
3. **Memento Pattern (`app/Services/Versioning/MementoService.php`):**
   - Captures immutable `PromptVersion` and `DocumentVersion` snapshots and supports `restorePromptVersion()` rollback.

---

## 3. Effort Estimation (Story Points)
| Module | Story Points | Est. Hours |
|---|---|---|
| Workspaces & 1-Click Guest Auth | 3 SP | 4 hrs |
| Documents, Chunks & Document Versioning | 8 SP | 10 hrs |
| Prompt Studio & Memento Versioning | 8 SP | 10 hrs |
| Strategy Families (Chunking, Retrieval, Rubric) | 13 SP | 15 hrs |
| EvaluationFacade & Deterministic Fallback | 10 SP | 12 hrs |
| Side-by-Side Runner & QA Metrics UI | 13 SP | 15 hrs |
| Automated QA Test Suite & Documentation | 13 SP | 18 hrs |
| **Total** | **68 SP** | **84 hrs** |

---

## 4. Risk Register
| ID | Risk | Likelihood / Impact | Mitigation Implemented |
|---|---|---|---|
| **R1** | External LLM API unavailable or rate-limited during grading | High / Critical | `GeminiProvider` automatically falls back to `DeterministicMockProvider` (`is_fallback = true`). |
| **R2** | MySQL vs. Neon PostgreSQL schema incompatibility | Medium / High | Portable Laravel migrations + standalone `database.sql` validated for MySQL 8.0+. |
| **R3** | Login barrier blocks live grading | Medium / High | 1-click `/demo/guest-login` route signs into pre-seeded demo workspace. |

---

## 5. QA Test Matrix & Execution Results
All 14 PHPUnit tests (43 assertions) pass via `php artisan test`:
- **Unit Tests:** `ChunkingStrategiesTest`, `RetrievalAndRubricStrategiesTest`
- **Boundary / Robustness Tests:** `BoundaryRobustnessTest` (empty text, zero/oversized chunk clamping, HTTP 429 API fallback)
- **Integration Tests:** `EvaluationFacadeIntegrationTest` (end-to-end RAG run, `[missing:var]` guard, Memento rollback)
- **Smoke Tests:** `GuestDemoSmokeTest`, `ExampleTest` (all primary routes HTTP 200, empty doc validation error, invalid ID 404)

---

## 6. Refactoring & Bug-Fix Log
1. **Phase 1 — Facade Extraction:** Extracted chunking, retrieval, and scoring out of `TestRunController` into `EvaluationFacade`.
2. **Phase 2 — Strategy Introduction:** Replaced hardcoded text splitting with `ChunkingStrategyInterface`, `RetrievalStrategyInterface`, and `EvaluationRubricStrategyInterface`.
3. **Phase 2 — Memento Versioning:** Added immutable `PromptVersion` and `DocumentVersion` snapshots via `MementoService`.
4. **Phase 3 — Boundary Guard:** Replaced missing `{{variables}}` with `[missing:var]` markers and clamped zero/negative chunk sizes in `FixedSizeChunkingStrategy`.
