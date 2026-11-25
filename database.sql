-- ============================================================================
-- AI-Assisted Knowledge & Prompt/RAG Evaluation Workbench (ITM 427 / ITM 428)
-- Standalone MySQL 8.0+ / MariaDB 10.4+ Compatible Schema & Seed Export
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `refactor_logs`;
DROP TABLE IF EXISTS `evaluation_scores`;
DROP TABLE IF EXISTS `test_run_results`;
DROP TABLE IF EXISTS `test_runs`;
DROP TABLE IF EXISTS `prompt_versions`;
DROP TABLE IF EXISTS `prompt_templates`;
DROP TABLE IF EXISTS `document_chunks`;
DROP TABLE IF EXISTS `document_versions`;
DROP TABLE IF EXISTS `documents`;
DROP TABLE IF EXISTS `workspaces`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- Table: users
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `role` VARCHAR(30) NOT NULL DEFAULT 'student',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: workspaces
-- ----------------------------------------------------------------------------
CREATE TABLE `workspaces` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `role_metadata` JSON NULL,
  `is_public_demo` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspaces_slug_unique` (`slug`),
  KEY `workspaces_user_id_foreign` (`user_id`),
  CONSTRAINT `workspaces_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: documents
-- ----------------------------------------------------------------------------
CREATE TABLE `documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `source_type` VARCHAR(30) NOT NULL DEFAULT 'paste',
  `raw_content` LONGTEXT NOT NULL,
  `content_hash` VARCHAR(64) NOT NULL,
  `chunk_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `current_version_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_workspace_id_foreign` (`workspace_id`),
  KEY `documents_content_hash_index` (`content_hash`),
  CONSTRAINT `documents_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: document_versions (Memento Snapshots)
-- ----------------------------------------------------------------------------
CREATE TABLE `document_versions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` BIGINT UNSIGNED NOT NULL,
  `version_number` INT UNSIGNED NOT NULL,
  `raw_content` LONGTEXT NOT NULL,
  `change_notes` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_versions_doc_ver_unique` (`document_id`, `version_number`),
  KEY `document_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `document_versions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: document_chunks
-- ----------------------------------------------------------------------------
CREATE TABLE `document_chunks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` BIGINT UNSIGNED NOT NULL,
  `chunk_index` INT UNSIGNED NOT NULL,
  `content` LONGTEXT NOT NULL,
  `token_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `strategy_used` VARCHAR(50) NOT NULL DEFAULT 'fixed_size',
  `metadata` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_chunks_doc_idx_index` (`document_id`, `chunk_index`),
  CONSTRAINT `document_chunks_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: prompt_templates
-- ----------------------------------------------------------------------------
CREATE TABLE `prompt_templates` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NULL,
  `current_version_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prompt_templates_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `prompt_templates_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: prompt_versions (Memento Snapshots)
-- ----------------------------------------------------------------------------
CREATE TABLE `prompt_versions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `prompt_template_id` BIGINT UNSIGNED NOT NULL,
  `version_number` INT UNSIGNED NOT NULL,
  `system_prompt` TEXT NOT NULL,
  `user_prompt` TEXT NOT NULL,
  `detected_variables` JSON NULL,
  `change_notes` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prompt_versions_tpl_ver_unique` (`prompt_template_id`, `version_number`),
  KEY `prompt_versions_created_by_foreign` (`created_by`),
  CONSTRAINT `prompt_versions_prompt_template_id_foreign` FOREIGN KEY (`prompt_template_id`) REFERENCES `prompt_templates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `prompt_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: test_runs
-- ----------------------------------------------------------------------------
CREATE TABLE `test_runs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `query_text` TEXT NOT NULL,
  `variables_payload` JSON NULL,
  `chunking_strategy` VARCHAR(50) NOT NULL DEFAULT 'fixed_size',
  `retrieval_strategy` VARCHAR(50) NOT NULL DEFAULT 'lexical_overlap',
  `evaluation_rubric` VARCHAR(50) NOT NULL DEFAULT 'grounding_faithfulness',
  `status` VARCHAR(30) NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_runs_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `test_runs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: test_run_results
-- ----------------------------------------------------------------------------
CREATE TABLE `test_run_results` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `test_run_id` BIGINT UNSIGNED NOT NULL,
  `prompt_version_id` BIGINT UNSIGNED NOT NULL,
  `model_identifier` VARCHAR(100) NOT NULL,
  `rendered_prompt` LONGTEXT NOT NULL,
  `retrieved_chunk_ids` JSON NULL,
  `generated_output` LONGTEXT NOT NULL,
  `latency_ms` INT UNSIGNED NOT NULL DEFAULT 0,
  `prompt_tokens` INT UNSIGNED NOT NULL DEFAULT 0,
  `completion_tokens` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_fallback` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_run_results_test_run_id_foreign` (`test_run_id`),
  KEY `test_run_results_prompt_version_id_foreign` (`prompt_version_id`),
  CONSTRAINT `test_run_results_test_run_id_foreign` FOREIGN KEY (`test_run_id`) REFERENCES `test_runs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_run_results_prompt_version_id_foreign` FOREIGN KEY (`prompt_version_id`) REFERENCES `prompt_versions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: evaluation_scores
-- ----------------------------------------------------------------------------
CREATE TABLE `evaluation_scores` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `test_run_result_id` BIGINT UNSIGNED NOT NULL,
  `rubric_type` VARCHAR(50) NOT NULL,
  `grounding_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `completeness_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `retrieval_relevance` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `overall_score` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `passed_regression` TINYINT(1) NOT NULL DEFAULT 0,
  `evaluator_notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evaluation_scores_test_run_result_id_foreign` (`test_run_result_id`),
  CONSTRAINT `evaluation_scores_test_run_result_id_foreign` FOREIGN KEY (`test_run_result_id`) REFERENCES `test_run_results` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: refactor_logs
-- ----------------------------------------------------------------------------
CREATE TABLE `refactor_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `workspace_id` BIGINT UNSIGNED NOT NULL,
  `sprint_phase` VARCHAR(50) NOT NULL,
  `component_modified` VARCHAR(120) NOT NULL,
  `design_pattern_applied` VARCHAR(100) NOT NULL,
  `issue_resolved` TEXT NOT NULL,
  `resolution_summary` TEXT NOT NULL,
  `commit_ref` VARCHAR(40) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `refactor_logs_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `refactor_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Realistic Seed Data
-- ============================================================================
INSERT INTO `users` (`id`, `name`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Dr. Evaluator (Guest Demo)', 'evaluator@zums.edu', 'evaluator', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(2, 'Moktadir (ITM 428)', 'moktadir@zums.edu', 'student', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

INSERT INTO `workspaces` (`id`, `user_id`, `name`, `slug`, `description`, `role_metadata`, `is_public_demo`, `created_at`, `updated_at`) VALUES
(1, 1, 'ITM 427/428 RAG Evaluation Lab', 'itm-428-rag-demo', 'Pre-seeded public demo workspace for evaluating chunking, retrieval, prompt versioning, and grounding rubrics.', '{"owner": "Dr. Evaluator", "course": "ITM 427 / ITM 428", "contributors": ["Moktadir"]}', 1, NOW(), NOW());

INSERT INTO `documents` (`id`, `workspace_id`, `title`, `source_type`, `raw_content`, `content_hash`, `chunk_count`, `current_version_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Software Testing & QA Handbook', 'paste', '# Software Testing & Boundary Value Analysis\n\nBoundary Value Analysis (BVA) is a black-box test design technique focusing on values at the edges of equivalence partitions.', SHA2('doc1', 256), 2, 1, NOW(), NOW()),
(2, 1, 'RAG Architecture & Design Patterns Guide', 'paste', '# Design Patterns in Enterprise Laravel\n\nEvaluationFacade hides chunking, retrieval, LLM invocation, and deterministic fallback behind one interface.', SHA2('doc2', 256), 2, 2, NOW(), NOW());

INSERT INTO `document_versions` (`id`, `document_id`, `version_number`, `raw_content`, `change_notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, '# Software Testing & Boundary Value Analysis\n\nBoundary Value Analysis (BVA) is a black-box test design technique focusing on values at the edges of equivalence partitions.', 'Initial QA handbook ingestion', 2, NOW()),
(2, 2, 1, '# Design Patterns in Enterprise Laravel\n\nEvaluationFacade hides chunking, retrieval, LLM invocation, and deterministic fallback behind one interface.', 'Initial architecture guide', 2, NOW());

INSERT INTO `document_chunks` (`id`, `document_id`, `chunk_index`, `content`, `token_count`, `strategy_used`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 0, 'Boundary Value Analysis (BVA) is a black-box test design technique focusing on edge values such as empty documents and missing template variables.', 28, 'paragraph_boundary', '{"strategy": "paragraph_boundary", "boundary_preserved": true}', NOW(), NOW()),
(2, 2, 0, 'EvaluationFacade implements the Facade Pattern, while Chunking, Retrieval, and Rubric classes implement the Strategy Pattern.', 24, 'paragraph_boundary', '{"strategy": "paragraph_boundary", "boundary_preserved": true}', NOW(), NOW());

INSERT INTO `prompt_templates` (`id`, `workspace_id`, `title`, `description`, `current_version_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'Strict Context-Grounded Q&A', 'Answers user queries strictly using retrieved knowledge base context with {{tone}} formatting.', 2, NOW(), NOW());

INSERT INTO `prompt_versions` (`id`, `prompt_template_id`, `version_number`, `system_prompt`, `user_prompt`, `detected_variables`, `change_notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, 'You are a helpful software engineering assistant. Answer based on context.', 'Context:\n{{context}}\n\nQuestion: {{query}}', '["context", "query"]', 'v1: Baseline prompt', 2, NOW()),
(2, 1, 2, 'You are a strict RAG evaluator. Use ONLY the provided context. Tone: {{tone}}.', '### Retrieved Context\n{{context}}\n\n### Query\n{{query}}', '["tone", "context", "query"]', 'v2: Added {{tone}} variable and strict grounding', 2, NOW());

INSERT INTO `test_runs` (`id`, `workspace_id`, `name`, `query_text`, `variables_payload`, `chunking_strategy`, `retrieval_strategy`, `evaluation_rubric`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Baseline vs. Strict Grounding Comparison', 'How do Facade, Strategy, and Boundary Value Analysis work together?', '{"tone": "academic and concise"}', 'paragraph_boundary', 'hybrid_similarity', 'grounding_faithfulness', 'completed', NOW(), NOW());

INSERT INTO `test_run_results` (`id`, `test_run_id`, `prompt_version_id`, `model_identifier`, `rendered_prompt`, `retrieved_chunk_ids`, `generated_output`, `latency_ms`, `prompt_tokens`, `completion_tokens`, `is_fallback`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'deterministic-mock-v1', 'SYSTEM: You are a helpful software engineering assistant...', '[1, 2]', '### Grounded Synthesis (Deterministic Fallback — Prompt v1)\n- EvaluationFacade implements the Facade Pattern and Strategy Pattern.\n- Boundary Value Analysis tests edge cases.', 19, 64, 42, 1, NOW(), NOW()),
(2, 1, 2, 'deterministic-mock-v1', 'SYSTEM: You are a strict RAG evaluator. Tone: academic and concise...', '[1, 2]', '### Grounded Synthesis (Deterministic Fallback — Prompt v2)\n- EvaluationFacade implements the Facade Pattern and Strategy Pattern.\n- Boundary Value Analysis tests edge cases.', 18, 72, 42, 1, NOW(), NOW());

INSERT INTO `evaluation_scores` (`id`, `test_run_result_id`, `rubric_type`, `grounding_score`, `completeness_score`, `retrieval_relevance`, `overall_score`, `passed_regression`, `evaluator_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'grounding_faithfulness', 84.50, 78.00, 82.00, 82.50, 1, 'Grounding rubric evaluated output against 2 retrieved chunks (threshold: 65.0%).', NOW(), NOW()),
(2, 2, 'grounding_faithfulness', 88.00, 82.50, 82.00, 85.73, 1, 'Grounding rubric evaluated output against 2 retrieved chunks (threshold: 65.0%).', NOW(), NOW());

INSERT INTO `refactor_logs` (`id`, `workspace_id`, `sprint_phase`, `component_modified`, `design_pattern_applied`, `issue_resolved`, `resolution_summary`, `commit_ref`, `created_at`, `updated_at`) VALUES
(1, 1, 'Phase 1', 'EvaluationFacade', 'Facade Pattern', 'Controllers previously coupled chunking, retrieval, and scoring logic.', 'Extracted orchestration into EvaluationFacade with DeterministicMockProvider fallback.', 'feat/facade', NOW(), NOW()),
(2, 1, 'Phase 2', 'Chunking & Retrieval Services', 'Strategy Pattern', 'Hardcoded text splitting caused mid-sentence context breaks.', 'Introduced ChunkingStrategyInterface, RetrievalStrategyInterface, and EvaluationRubricStrategyInterface.', 'feat/strategy', NOW(), NOW()),
(3, 1, 'Phase 2', 'MementoService', 'Memento Pattern', 'Editing prompt templates overwrote previous states.', 'Added immutable PromptVersion and DocumentVersion snapshots with rollback.', 'feat/memento', NOW(), NOW()),
(4, 1, 'Phase 3', 'EvaluationFacade::interpolate', 'Boundary Guard', 'Missing {{variables}} left raw curly braces in prompts.', 'Replaced missing variables deterministically with [missing:var] markers.', 'fix/bva-vars', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
