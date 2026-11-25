<?php

namespace App\Enums;

enum TestRunStatusEnum: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
