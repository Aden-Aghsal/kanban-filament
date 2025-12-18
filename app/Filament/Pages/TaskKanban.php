<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Enums\TaskStatus;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

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
            TaskStatus::TODO->value => '📝 Todo',
            TaskStatus::IN_PROGRESS->value => '⚙️ In Progress',
            TaskStatus::DONE->value => '✅ Done',
            TaskStatus::CANCELED->value => '❌ Canceled',
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

        if (! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->get();
    }

    /* =======================
       DRAG & DROP (ENUM SAFE)
    ======================= */
    public function onStatusChanged(
        string|int $recordId,
        string $status,
        array $fromOrderedIds,
        array $toOrderedIds
    ): void {
        $task = Task::findOrFail($recordId);

        if (! auth()->user()->can('update', $task)) {
            throw new AuthorizationException('Unauthorized action.');
        }

        $newStatus = TaskStatus::from($status);

        // lock DONE untuk user biasa
        if (! auth()->user()->isAdmin() && $newStatus === TaskStatus::DONE) {
            Notification::make()
                ->title('Tidak diizinkan')
                ->danger()
                ->send();
            return;
        }

        // cancel pakai modal
        if ($newStatus === TaskStatus::CANCELED) {
            $this->mountAction('cancelTask', [
                'task_id' => $task->id,
            ]);
            return;
        }

        // update normal
        $task->update([
            'status' => $newStatus,
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
                ->action(function (array $data, array $arguments) {
                    $task = Task::findOrFail($arguments['task_id']);

                    if (! auth()->user()->can('cancel', $task)) {
                        abort(403);
                    }

                    $task->update([
                        'status' => TaskStatus::CANCELED,
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
}
