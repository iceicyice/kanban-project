<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Management';

    // public static function canAccess(): bool
    // {
    //     return auth()->user()->roles() == 'Admin';
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->filled()
                        ->required(fn (Page $livewire) => ($livewire instanceof CreateRecord)),
                    TextInput::make('email')
                        ->email()
                        ->filled()
                        ->required(fn (Page $livewire) => ($livewire instanceof CreateRecord)),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (Page $livewire) => ($livewire instanceof CreateRecord))
                        ->minLength(8)
                        ->maxLength(16),
                    Section::make([
                        Select::make('role')
                            ->multiple()
                            ->relationship('roles', 'name')->preload(),
                        Select::make('permission')
                            ->multiple()
                            ->relationship('permissions', 'name')->preload(),
                    ])
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('roles.name')
                ->sortable(),
                TextColumn::make('email_verified_at'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
