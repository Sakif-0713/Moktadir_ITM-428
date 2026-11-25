<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default RAG Pipeline Configuration
    |--------------------------------------------------------------------------
    */
    'default_chunk_size' => (int) env('RAG_CHUNK_SIZE', 450),
    'default_chunk_overlap' => (int) env('RAG_CHUNK_OVERLAP', 60),
    'default_top_k' => (int) env('RAG_TOP_K', 3),
    'regression_pass_threshold' => (float) env('RAG_PASS_THRESHOLD', 65.0),

    /*
    |--------------------------------------------------------------------------
    | LLM Provider & Deterministic Fallback
    |--------------------------------------------------------------------------
    */
    'provider' => env('RAG_LLM_PROVIDER', 'gemini'),
    'force_mock_fallback' => (bool) env('RAG_FORCE_MOCK', false),
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'timeout_seconds' => (int) env('GEMINI_TIMEOUT', 15),
    ],
];
