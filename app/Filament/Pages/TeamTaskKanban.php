<?php

namespace App\Filament\Pages;

use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use App\Models\Task;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class TeamTaskKanban extends KanbanBoard
{
    protected static ?string $navigationLabel = 'Team Task Board';
    protected static ?string $navigationGroup = 'Task Management';
    // protected static ?string $navigationIcon = 'heroicon-o-clipboard-check';

    protected static bool $shouldRegisterNavigation = true;

    protected static string $model = Task::class;
    protected static string $statusEnum = \App\Enums\TaskStatus::class;

    /**
     * Ambil task tim yang user ikut
     */
    protected function records(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            throw new AuthorizationException();
        }

        $teamIds = $user->teams->pluck('id');

        return Task::query()
            ->whereIn('team_id', $teamIds)
            ->with(['team', 'user'])
            ->get();
    }

    protected function cardView(): string
    {
        return 'filament.kanban.task-card';
    }

    /**
     * Batasi drag & drop hanya anggota team
     */
    public function canMoveRecord($record): bool
    {
        $user = auth()->user();
        return $user && $user->teams->contains($record->team_id);
    }
}
