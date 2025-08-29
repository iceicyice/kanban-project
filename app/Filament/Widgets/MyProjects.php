<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\TaskProject;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Exports\TaskProjectExporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Actions\Exports\Enums\ExportFormat;

class MyProjects extends BaseWidget
{
    // protected static ?string $model = TaskProject::class;

    public function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(5)
            ->query(
                TaskProject::query()
                    ->whereHas('users', fn($q) => 
                        $q->where('users.id', auth()->id())
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->url(fn ($record) => url('/tasks-kanban?project=' . $record->id)),
                TextColumn::make('description')
                    ->searchable(),
                // TextColumn::make('users.name')
                //     ->formatStateUsing(fn($state, $record) => 
                //         $record->users->take(3)->pluck('name')->join(', ') . 
                //         ($record->users->count() > 3 ? ', ...' : '')
                //     )
                //     ->tooltip(fn($state, $record) => 
                //         $record->users->pluck('name')),
                TextColumn::make('deadline')
                    ->label('Deadline')
                    ->sortable(),
                TextColumn::make('progress')
                    ->label('Progress')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->color(fn ($state) => $state < 50 ? 'danger' : 'success'),
            ])
            ->bulkActions([
                
                    ExportBulkAction::make()
                        ->label('Export')
                        ->exporter(TaskProjectExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ])
            ]);
    }
}
