<?php

namespace App\Enums;

enum EvaluationRubricEnum: string
{
    case GROUNDING_FAITHFULNESS = 'grounding_faithfulness';
    case COMPLETENESS_STRUCTURE = 'completeness_structure';

    public function label(): string
    {
        return match ($this) {
            self::GROUNDING_FAITHFULNESS => 'Context Grounding & Faithfulness',
            self::COMPLETENESS_STRUCTURE => 'Completeness & Response Structure',
        };
    }
}
