<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Illuminate\Support\Collection;
use App\Enums\TaskStatus;

class UserKanban extends KanbanBoard
{
    protected static bool $shouldRegisterNavigation = false;

    //  untuk KanbanBoard
    protected static string $model = Task::class;
       protected static string $statusEnum = TaskStatus::class;

    public ?User $user = null;

    public function mount(): void
    {
        parent::mount();

        //  Hanya admin
        if (! auth()->user() || ! auth()->user()->hasRole('admin')) {
            throw new AuthorizationException();
        }

        //  Ambil user dari query string
        $userId = request()->query('user');
        abort_if(! $userId, 404);

        $this->user = User::findOrFail($userId);
    }

    /**
     * Task milik user terpilih
     */
    protected function records(): Collection
    {
        return $this->user
            ->tasks()
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Custom card
     */
    protected function cardView(): string
    {
        return 'filament.kanban.task-card';
    }
}
