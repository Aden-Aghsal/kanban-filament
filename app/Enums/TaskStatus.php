<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case CANCELED = 'canceled';

    public static function statuses(): Collection
    {
        return collect([
            self::TODO->value => [
                'id'    => self::TODO->value,
                'title' => 'Todo',
                'label' => 'Todo',
            ],
            self::IN_PROGRESS->value => [
                'id'    => self::IN_PROGRESS->value,
                'title' => 'In Progress',
                'label' => 'In Progress',
            ],
            self::DONE->value => [
                'id'    => self::DONE->value,
                'title' => 'Done',
                'label' => 'Done',
            ],
            self::CANCELED->value => [
                'id'    => self::CANCELED->value,
                'title' => 'Canceled',
                'label' => 'Canceled',
            ],
        ]);
    }
}
