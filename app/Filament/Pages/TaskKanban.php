<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Enums\TaskStatus;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;

class TaskKanban extends KanbanBoard
{
    protected static string $model = Task::class;
    protected static string $statusEnum = TaskStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?string $navigationGroup = 'Task Management';

    /* =======================
       UI / UX
    ======================= */
    protected function getColumnHeader(string $status): string
    {
        return match ($status) {
            TaskStatus::TODO->value => 'Todo',
            TaskStatus::IN_PROGRESS->value => 'In Progress',
            TaskStatus::DONE->value => 'Done',
            TaskStatus::CANCELED->value => 'Canceled',
            default => ucfirst($status),
        };
    }

    protected function getCardView(): string
    {
        return 'filament.kanban.task-card';
    }

    /* =======================
       DATA
    ======================= */
    protected function records(): Collection
    {
        $query = Task::query();

        // User biasa lihat task sendiri, admin lihat semua
        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->get();
    }

    /* =======================
       DRAG & DROP (BEBAS + NOTIF)
    ======================= */
    public function onStatusChanged(
        string|int $recordId,
        string $status,
        array $fromOrderedIds,
        array $toOrderedIds
    ): void {
        $task = Task::findOrFail($recordId);

        // Update status
        $task->update([
            'status' => TaskStatus::from($status),
            'canceled_at' => null,
            'canceled_reason' => null,
        ]);

        // Notifikasi toast ringan
        Notification::make()
            ->title("Task \"{$task->title}\" dipindahkan ke {$status}")
            ->success()
            ->send();
    }
}
