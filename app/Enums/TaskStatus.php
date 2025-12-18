<?php

namespace App\Enums;

use Mokhosh\FilamentKanban\Concerns\IsKanbanStatus;

enum TaskStatus: string
{
    use IsKanbanStatus;

    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'Todo',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
            self::CANCELED => 'Canceled',
        };
    }
}
