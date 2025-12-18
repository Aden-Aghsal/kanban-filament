<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    
    protected static ?string $model = \App\Models\Task::class;
protected static ?string $navigationIcon = 'heroicon-o-clipboard';
protected static ?string $navigationGroup = 'Task Management';
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),

            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->options([
                    'todo' => 'Todo',
                    'in_progress' => 'In Progress',
                    'done' => 'Done',
                    'canceled' => 'Canceled',
                ])
                ->required(),

            Forms\Components\Select::make('priority')
                ->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                ])
                ->default('medium'),

            Forms\Components\DatePicker::make('deadline'),

            Forms\Components\Textarea::make('canceled_reason')
                ->visible(fn ($get) => $get('status') === 'canceled'),
        ]);
    }

    public static function table(Table $table): Table
    {
       return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')
                ->searchable(),

            Tables\Columns\TextColumn::make('user.name')
                ->label('Owner'),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'todo',
                    'info' => 'in_progress',
                    'success' => 'done',
                    'danger' => 'canceled',
                ]),

            Tables\Columns\BadgeColumn::make('priority'),

            Tables\Columns\TextColumn::make('deadline')
                ->date(),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    if (! auth()->user()->isAdmin()) {
        $query->where('user_id', auth()->id());
    }

    return $query;
}

public static function canEdit($record): bool
{
    return auth()->user()->can('update', $record);
}

public static function canDelete($record): bool
{
    return auth()->user()->can('delete', $record);
}

}
