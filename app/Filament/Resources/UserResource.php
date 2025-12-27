<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

    class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static ?string $navigationGroup = 'User Management';


   public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('name')
            ->required(),

        Forms\Components\TextInput::make('email')
            ->email()
            ->required(),

        Forms\Components\Select::make('role')
            ->options([
                'admin' => 'Admin',
                'user' => 'User',
            ])
            ->required(),

        Forms\Components\TextInput::make('password')
            ->password()
            ->label('Password')
            ->dehydrateStateUsing(fn ($state) =>
                filled($state) ? Hash::make($state) : null
            )
            ->dehydrated(fn ($state) => filled($state))
            ->required(fn ($context) => $context === 'create'),

        Forms\Components\TextInput::make('password_confirmation')
            ->password()
            ->same('password')
            ->dehydrated(false),
    ]);
}


    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->searchable(),

            Tables\Columns\BadgeColumn::make('role')
                ->colors([
                    'danger' => 'admin',
                    'primary' => 'user',
                ])
                ->label('Role'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),
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
        'index' => Pages\ListUsers::route('/'),
        'create' => Pages\CreateUser::route('/create'),
        'edit' => Pages\EditUser::route('/{record}/edit'),
    ];
}

}
