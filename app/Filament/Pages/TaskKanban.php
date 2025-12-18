<?php

namespace App\Filament\Pages;

use App\Models\Task;
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
}