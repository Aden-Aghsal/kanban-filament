<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use App\Enums\TaskStatus;

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
            'todo' => '📝 Todo',
            'in_progress' => '⚙️ In Progress',
            'done' => '✅ Done',
            'canceled' => '❌ Canceled',
            default => ucfirst($status),
        };
    }

    protected function getCardView(): string
    {
        return 'filament.kanban.task-card'; // buat di resources/views/filament/kanban/task-card.blade.php
    }

    /* =======================
       DATA (ANTI INSPECT)
    ======================= */
    protected function records(): Collection
    {
        $query = Task::query();

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->get();
    }

    /* =======================
       DRAG & DROP (SECURE)
    ======================= */
    public function onStatusChanged(string|int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        $task = Task::findOrFail($recordId);

        // server-side permission check
        if (! auth()->user()->can('update', $task)) {
            throw new AuthorizationException('Unauthorized action.');
        }

        // lock done untuk user biasa
        if (! auth()->user()->isAdmin() && $status === 'done') {
            Notification::make()
                ->title('Tidak diizinkan')
                ->danger()
                ->send();
            return;
        }

        // logic cancel
        if ($status === 'canceled') {
            $this->mountAction('cancelTask', [
                'task_id' => $task->id,
            ]);
            return;
        }

        // update status normal
        $task->update([
            'status' => $status,
            'canceled_at' => null,
            'canceled_reason' => null,
        ]);
    }

    /* =======================
       ACTION: CANCEL TASK
    ======================= */
    protected function getActions(): array
    {
        return [
            Action::make('cancelTask')
                ->label('Cancel Task')
                ->modalHeading('Cancel Task')
                ->modalDescription('Masukkan alasan pembatalan task')
                ->form([
                    Forms\Components\Textarea::make('canceled_reason')
                        ->label('Alasan Cancel')
                        ->required()
                        ->rows(3),
                ])
                ->action(function(array $data, array $arguments) {
                    $task = Task::findOrFail($arguments['task_id']);

                    if (! auth()->user()->can('cancel', $task)) {
                        abort(403);
                    }

                    $task->update([
                        'status' => 'canceled',
                        'canceled_at' => now(),
                        'canceled_reason' => $data['canceled_reason'],
                    ]);

                    Notification::make()
                        ->title('Task berhasil dicancel')
                        ->success()
                        ->send();
                }),
        ];
    }

    /* =======================
       HELPERS
    ======================= */
    protected function canCancel(Task $task): bool
    {
        $user = auth()->user();
        return $user->isAdmin() || $task->user_id === $user->id;
    }
}
