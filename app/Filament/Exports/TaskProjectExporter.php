<?php

namespace App\Filament\Exports;

use App\Models\TaskProject;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TaskProjectExporter extends Exporter
{
    protected static ?string $model = TaskProject::class;
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['users', 'tasks', 'tasks.user', 'tasks.status']);
    }

    public function export($records)
    {
        // Ensure the selected records are passed
        return parent::export($records); // Export only the passed records
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Project Name'),
            ExportColumn::make('description'),
            ExportColumn::make('progress'),
            ExportColumn::make('deadline'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),

            // Export user names (comma-separated)
            ExportColumn::make('users')
                ->label('Assigned Users')
                ->formatStateUsing(fn ($record) => 
                    $record->users->pluck('name')->join(', ')
                ),

            // Export tasks (titles only, or expand if needed)
            ExportColumn::make('tasks')
                ->label('Tasks')
                ->formatStateUsing(fn ($record) =>
                    $record->tasks->pluck('title')->join(', ')
                ),

            // Optionally, export task count
            ExportColumn::make('tasks_count')
                ->label('Number of Tasks')
                ->formatStateUsing(fn ($record) => $record->tasks->count()),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your task project export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
