<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Filament\Pages\UserKanban;
use Illuminate\Auth\Access\AuthorizationException;

class AdminUserTasks extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.admin-user-tasks';
    protected static ?string $navigationLabel = 'User Tasks';
    protected static ?string $title = 'User Task Overview';

    /**
     * 🔐 Hanya admin boleh akses halaman ini
     */
    public function mount(): void
    {
        if (! auth()->user() || ! auth()->user()->hasRole('admin')) {
            throw new AuthorizationException();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->withCount('tasks')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama User')
                    ->searchable(),

                Tables\Columns\TextColumn::make('id')
                    ->label('User ID'),

                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Jumlah Task'),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat_board')
                    ->label('Lihat Board')
                    ->icon('heroicon-o-view-columns')
                    ->url(fn (User $record) =>
                        UserKanban::getUrl(['user' => $record->id])
                    ),
            ]);
    }
}
