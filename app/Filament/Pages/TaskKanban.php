<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Forms;
use Filament\Notifications\Notification;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection; // PENTING: Gunakan Collection, bukan Builder

class TaskKanban extends KanbanBoard
{
    protected static string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Kanban Board';
    protected static ?string $navigationGroup = 'Task Management';

    // Header Kolom dengan Emoji (Sudah Bagus!)
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

    // View Kustom untuk Kartu
    protected function getCardView(): string
    {
        // Pastikan file ini ada di: resources/views/filament/kanban/task-card.blade.php
        return 'filament.kanban.task-card';
    }

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
     * PERBAIKAN: Mengubah return type menjadi Collection 
     * dan menambahkan ->get() untuk menjalankan query.
     */
    protected function records(): Collection
    {
        $query = Task::query();

        // Jika bukan admin, hanya ambil task miliknya
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query->get(); // Mengembalikan Collection (Hasil Data)
    }

    protected function onStatusChanged($record, string $status): void
{
    // Kalau bukan cancel → update normal
    if ($status !== 'canceled') {
        $record->update([
            'status' => $status,
            'canceled_at' => null,
            'canceled_reason' => null,
        ]);

        return;
    }

    // Kalau cancel → buka modal
    $this->mountAction('cancelTask', [
        'task_id' => $record->id,
    ]);
}

protected function getActions(): array
{
    return [
        Forms\Components\Actions\Action::make('cancelTask')
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

    if (! $this->canCancel($task)) {
        Notification::make()
            ->title('Tidak diizinkan')
            ->danger()
            ->send();

        return;
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

protected function canCancel(Task $task): bool
{
    $user = auth()->user();

    return $user->isAdmin() || $task->user_id === $user->id;
}


}