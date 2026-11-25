<?php

namespace App\Enums;

enum ChunkingStrategyEnum: string
{
    case FIXED_SIZE = 'fixed_size';
    case PARAGRAPH_BOUNDARY = 'paragraph_boundary';

    public function label(): string
    {
        return match ($this) {
            self::FIXED_SIZE => 'Fixed-Size Window (Overlap)',
            self::PARAGRAPH_BOUNDARY => 'Paragraph & Boundary-Aware',
        };
    }
}
