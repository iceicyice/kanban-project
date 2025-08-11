<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TaskProject;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskProjectResource\Pages;
use App\Filament\Resources\TaskProjectResource\RelationManagers;

class TaskProjectResource extends Resource
{
    protected static ?string $model = TaskProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Project Management';
    
    protected static ?string $navigationLabel = 'Projects';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->label('Project Name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('description')
                        ->required()
                        ->maxLength(255),
                    Select::make('users')
                        ->multiple()
                        ->relationship('users', 'name')->preload()
                        ->required(),
                    DatePicker::make('deadline')
                ])->columns(2),
                Card::make()->schema([
                    Repeater::make('statuses')
                        ->relationship('statuses', modifyQueryUsing: fn ($query) => $query->orderBy('order_column')) // Order by order_column
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('Status Name'),
                            TextInput::make('order_column')
                                ->numeric()
                                ->label('Status Order Column'),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Add Status')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('users.name')
                    ->formatStateUsing(fn($state, $record) => 
                        $record->users->take(3)->pluck('name')->join(', ') . 
                        ($record->users->count() > 3 ? ', ...' : '')
                    )
                    ->tooltip(fn($state, $record) => 
                        $record->users->pluck('name')),
                TextColumn::make('statuses')
                    ->formatStateUsing(fn($state, $record) => 
                        $record->statuses->count() . ' Status'
                    )
                    ->tooltip(fn($state, $record) => 
                        $record->statuses->pluck('name')),
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->color(fn ($state) => $state < 50 ? 'danger' : 'success'),
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
            'index' => Pages\ListTaskProjects::route('/'),
            'create' => Pages\CreateTaskProject::route('/create'),
            'edit' => Pages\EditTaskProject::route('/{record}/edit'),
        ];
    }
}
