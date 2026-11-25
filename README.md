# PromptLab — AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php)
![PHPUnit](https://img.shields.io/badge/Tests-14%20Passed%20(43%20Assertions)-10B981?style=flat-square)
![Course](https://img.shields.io/badge/ZUMS-ITM%20427%20%2F%20ITM%20428%20Lab-2563EB?style=flat-square)

An enterprise-style **Software Engineering & RAG Evaluation Workbench** built with **Laravel MVC**. Designed for **ZUMS ITM 427 (Software Engineering)** & **ITM 428 (Software Engineering Lab)** aligned with **CLO1–CLO4**.

PromptLab enables teams to ingest knowledge documents, test interchangeable chunking and retrieval strategies, version prompt templates with dynamic `{{variables}}`, run side-by-side comparisons, and inspect automated grounding/completeness quality scores—complete with a **1-Click Public Evaluator Demo** and a **deterministic offline fallback** so evaluations never fail without external API keys.

---

## ✨ Key Features

- **1-Click Evaluator Demo (`/demo/guest-login`):** Zero-login friction for instructors and reviewers; launches directly into a pre-seeded workspace.
- **Interactive 3D Homepage & SaaS UI:** 1-second intro splash, ambient pastel mesh background, and a 3D scroll-driven pipeline walkthrough explaining how RAG evaluation works to any visitor.
- **Workspaces & Role Metadata:** Multi-workspace CRUD with ownership/contributor metadata.
- **Knowledge Base & Chunking Engine:** Upload `.txt`/`.md` files or paste text, inspect generated chunks, switch chunking strategies, and track document revision history.
- **Prompt Studio (Memento Pattern):** Edit system/user prompts with automatic `{{variable}}` detection, immutable version snapshots (`PromptVersion`), and one-click rollback.
- **Side-by-Side Test Runner (Facade Pattern):** Compare 1–4 prompt versions on the same query with latency (`ms`), token usage, retrieved chunks, and Pass/Fail regression status.
- **QA & Refactoring Audit (`RefactorLog`):** In-app traceability log documenting design pattern introductions and bug fixes.

---

## 🏛️ Software Engineering & Design Patterns

| Pattern | Implementation | Purpose |
|---|---|---|
| **Facade Pattern** | `App\Services\Facades\EvaluationFacade` | Unifies chunking, top-k retrieval, `{{variable}}` interpolation, Gemini API calls, automatic `DeterministicMockProvider` fallback, and rubric scoring behind one clean interface. |
| **Strategy Pattern** | `App\Services\RAG\Chunking\*`<br>`App\Services\RAG\Retrieval\*`<br>`App\Services\RAG\Evaluation\*` | Runtime-swappable algorithms for **Chunking** (*Fixed-Size* vs. *Paragraph-Boundary*), **Retrieval** (*Lexical Overlap* vs. *Hybrid Similarity*), and **Evaluation Rubrics** (*Grounding/Faithfulness* vs. *Completeness/Structure*). |
| **Memento Pattern** | `App\Services\Versioning\MementoService` | Captures immutable `PromptVersion` and `DocumentVersion` snapshots and supports safe rollback without losing history. |

---

## 🚀 Local Setup: Laravel (`php artisan`) + MySQL

### Prerequisites
- **PHP 8.3+** (`pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`)
- **Composer 2.x**
- **MySQL 8.0+** or **MariaDB 10.4+** (or SQLite for quick local testing)

### Installation Steps
```bash
# 1. Clone the repository
git clone https://github.com/Sakif-0713/Moktadir_ITM-428.git
cd Moktadir_ITM-428

# 2. Install PHP dependencies
composer install

# 3. Create environment file & generate app key
cp .env.example .env
php artisan key:generate
```

Update `.env` for **MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rag_workbench
DB_USERNAME=root
DB_PASSWORD=
```

Create the `rag_workbench` database in MySQL/phpMyAdmin, then migrate, seed, and serve:
```bash
php artisan migrate --seed
php artisan serve
```
Open **http://127.0.0.1:8000** and click **Get Started / Launch 1-Click Live Demo**.

---

## 🗄️ Standalone `database.sql` Import (phpMyAdmin / MySQL CLI)
A clean, standalone, uncompressed [`database.sql`](database.sql) file is included in the repository root with `CREATE TABLE` statements, foreign keys, indexes, and realistic seed rows.

```bash
mysql -u root -p rag_workbench < database.sql
```
*(Or in **phpMyAdmin**: select `rag_workbench` → **Import** → choose `database.sql` → **Go**.)*

---

## ☁️ Cloud Deployment (Neon PostgreSQL)
For live cloud grading, configure `.env` on your host (Render / Railway / Fly.io) to connect to **Neon**:
```env
DB_CONNECTION=pgsql
DB_URL=postgresql://<user>:<password>@<neon-host>/<dbname>?sslmode=require
```
If `GEMINI_API_KEY` is omitted or rate-limited, `EvaluationFacade` automatically switches to `DeterministicMockProvider` (`is_fallback = true`) so all runs succeed.

---

## 🧪 Running the Automated QA Test Suite
```bash
php artisan test
```
**Latest Verification:** `14 passed, 43 assertions` across:
- **Unit Tests:** Chunking, Retrieval, and Evaluation Rubric Strategies
- **Boundary & Robustness Tests:** Empty documents, zero/oversized chunk clamping, missing `{{variables}}`, and simulated HTTP 429 API fallback
- **Integration Tests:** End-to-end `EvaluationFacade` pipeline & `MementoService` rollback
- **Smoke Tests:** 1-click guest login, all primary routes HTTP 200, validation errors, and 404 handling

---

## 📄 Academic Documentation (`docs/`)
- [Project Proposal](docs/proposal.md)
- [Final Submission Report (`.docx`-ready)](docs/final_submission_report.md)
