<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Database\Eloquent\Builder;

class TaskKanban extends KanbanBoard
{
    protected static string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?string $navigationGroup = 'Task Management';

    /**
     * Definisi kolom kanban
     */
    protected function getStatuses(): array
    {
        return [
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'done' => 'Done',
            'canceled' => 'Canceled',
        ];
    }

    /**
     * Query data task
     * Admin: semua task
     * User: task miliknya
     */
    protected function records(): Builder
    {
        return auth()->user()->isAdmin()
            ? Task::query()
            : Task::where('user_id', auth()->id());
    }
}
