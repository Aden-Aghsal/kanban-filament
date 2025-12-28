<?php

namespace App\Filament\User\Pages;

use App\Models\Task;
use App\Enums\TaskStatus;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;

class TaskKanban extends KanbanBoard
{
    protected static string $model = Task::class;
    protected static string $statusEnum = TaskStatus::class;

    protected static ?string $navigationGroup = 'Task Management';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    /* =======================
       UI / UX
    ======================= */
    protected function getColumnHeader(string $status): string
    {
        return match ($status) {
            TaskStatus::TODO->value => '📝 Todo',
            TaskStatus::IN_PROGRESS->value => '⚙️ In Progress',
            TaskStatus::DONE->value => '✅ Done',
            TaskStatus::CANCELED->value => '❌ Canceled',
            default => ucfirst($status),
        };
    }

    protected function getCardView(): string
    {
        return 'filament.kanban.user-task-card';
    }

    /* =======================
       DATA
    ======================= */
    protected function records(): Collection
    {
        return Task::where('user_id', auth()->id())
            ->orderByRaw("FIELD(priority, 'high','medium','low')")
            ->get();
    }

    /* =======================
       DRAG & DROP (BEBAS)
    ======================= */
    public function onStatusChanged(
        string|int $recordId,
        string $status,
        array $fromOrderedIds,
        array $toOrderedIds
    ): void {
        $task = Task::findOrFail($recordId);

        // update status langsung, bebas drag & drop
        $task->update([
            'status' => TaskStatus::from($status),
            'canceled_at' => $status === TaskStatus::CANCELED->value ? now() : null,
            'canceled_reason' => $status === TaskStatus::CANCELED->value ? 'Dicancel via drag & drop' : null,
        ]);

        // notifikasi toast ringan
        Notification::make()
            ->title("Task \"{$task->title}\" dipindahkan ke {$status}")
            ->success()
            ->send();
    }
}
