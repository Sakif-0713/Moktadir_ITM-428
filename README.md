# AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench
**Course:** ZUMS **ITM 427 (Software Engineering)** & **ITM 428 (Software Engineering Lab)**  
**Alignment:** CLO1–CLO4 (Requirements & Estimation, Design Patterns, MVC Implementation, QA & Technical Documentation)

---

## 1. Quick Instructor / Evaluator Demo Access
The application includes a **1-Click Public Demo / Guest Access** path so evaluators can test all features immediately without registration or login barriers:
1. Start the app (or visit the live hosted URL).
2. Click **"1-Click Evaluator Demo"** in the top navigation bar (or visit `/demo/guest-login`).
3. You are automatically signed into the pre-seeded **`ITM 427/428 RAG Evaluation Lab`** workspace containing sample documents, generated chunks, prompt versions, side-by-side test runs, and refactoring logs.

---

## 2. Local Setup: Laravel (`php artisan`) + MySQL
### Prerequisites
- PHP 8.3+ (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`)
- Composer 2.x
- MySQL 8.0+ or MariaDB 10.4+

### Step-by-Step Installation
```bash
# 1. Clone the repository
git clone <your-github-repo-url>
cd Moktadir_ITM-428

# 2. Install PHP dependencies
composer install

# 3. Configure environment for local MySQL
cp .env.example .env
php artisan key:generate
```

Update `.env` with your local MySQL credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rag_workbench
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

Create the MySQL database (`rag_workbench`), then run migrations, seeders, and start the server:
```bash
php artisan migrate --seed
php artisan serve
```
Visit `http://127.0.0.1:8000` in your browser.

---

## 3. Standalone `database.sql` Import (phpMyAdmin / MySQL CLI)
A clean, standalone, uncompressed `database.sql` file is included in the repository root.
- **Via phpMyAdmin:** Create/select database `rag_workbench` -> **Import** -> Choose `database.sql` -> **Go**.
- **Via MySQL CLI:**
  ```bash
  mysql -u root -p rag_workbench < database.sql
  ```

---

## 4. Cloud Deployment (Neon PostgreSQL)
For live cloud grading, configure `.env` on your host (Render / Railway / Fly.io) to use **Neon**:
```env
DB_CONNECTION=pgsql
DB_URL=postgresql://<user>:<password>@<neon-host>/<dbname>?sslmode=require
```
If `GEMINI_API_KEY` is omitted or rate-limited, `EvaluationFacade` automatically switches to `DeterministicMockProvider` so evaluation runs never fail.

---

## 5. Running the Automated QA Test Suite
```bash
php artisan test
```
Covers **Unit Tests** (Chunking, Retrieval, Rubric Strategies), **Integration Tests** (`EvaluationFacade`, `MementoService` rollback), **Boundary Value / Robustness Tests** (empty docs, zero chunk size, missing `{{variables}}`, HTTP 429 fallback), and **Smoke Tests**.

---

## 6. Project Documentation
- [Project Proposal](docs/proposal.md)
- [Final Submission Report (`.docx`-ready)](docs/final_submission_report.md)
