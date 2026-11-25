<?php

namespace App\Enums;

enum RetrievalStrategyEnum: string
{
    case LEXICAL_OVERLAP = 'lexical_overlap';
    case HYBRID_SIMILARITY = 'hybrid_similarity';

    public function label(): string
    {
        return match ($this) {
            self::LEXICAL_OVERLAP => 'Lexical / Keyword Overlap (BM25-style)',
            self::HYBRID_SIMILARITY => 'Hybrid Lexical + N-Gram Similarity',
        };
    }
}
