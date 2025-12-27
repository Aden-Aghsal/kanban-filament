<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationGroup = 'Task Management';
protected static ?string $navigationLabel = 'Tasks';
protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    

    /* =======================
       FORM
    ======================= */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required(),

            Forms\Components\Textarea::make('description'),
            
            Forms\Components\Select::make('priority')
    ->label('Priority')
    ->options([
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ])
    ->default('medium') // bisa default medium
    ->required(),

            Forms\Components\Select::make('status')
                ->options([
                    'todo' => 'Todo',
                    'in_progress' => 'In Progress',
                    'done' => 'Done',
                ])
                ->default('todo'),

            Forms\Components\DatePicker::make('deadline'),
            Forms\Components\Hidden::make('user_id')
    ->default(fn () => auth()->id()),

        ]);
    }

    /* =======================
       TABLE
    ======================= */
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')
                ->searchable(),

            Tables\Columns\TextColumn::make('description')
                ->limit(50)
                ->wrap(),  

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'todo',
                    'info' => 'in_progress',
                    'success' => 'done',
                    'danger' => 'canceled',
                ]),

            Tables\Columns\BadgeColumn::make('priority')
                ->colors([
                    'success' => 'low',
                    'warning' => 'medium',
                    'danger' => 'high',
                ]),

            Tables\Columns\TextColumn::make('deadline')->date(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
}

    /* =======================
       FILTER DATA USER
    ======================= */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
