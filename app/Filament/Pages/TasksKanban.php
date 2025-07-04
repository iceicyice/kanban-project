<?php

namespace App\Filament\Pages;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Models\TaskUser;
use Illuminate\Support\Collection;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Model;
use Filament\Forms\Components\Select;

class TasksKanban extends KanbanBoard
{

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Tasks';
    protected static string $model = Task::class;
    protected static string $statusEnum = TaskStatus::class;


    protected static string $headerView = 'tasks.kanban-header';

    protected static string $recordView = 'tasks.kanban-record';

    protected static string $statusView = 'tasks.kanban-status';




    protected function statuses(): Collection
    {
        return TaskStatus::statuses();
    }

    protected function records(): Collection
    {
        // return $this->getEloquentQuery()
        //     ->when(method_exists(static::$model, 'scopeOrdered'), fn ($query) => $query->ordered())
        //     ->get();

        return Task::ordered()->get();
    }

    public function onStatusChanged(string|int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Task::find($recordId)->update(['status' => $status]);
        Task::ignoreTimestamps();
        Task::setNewOrder($toOrderedIds);
        Task::ignoreTimestamps(false);
    }

    public function onSortChanged(string|int $recordId, string $status,array $orderedIds): void
    {
        Task::ignoreTimestamps();
        Task::setNewOrder($orderedIds);
        Task::ignoreTimestamps(false);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();

                    return $data;
                })
                ->model(Task::class)
                ->form(
                    [
                        TextInput::make('title'),
                        Textarea::make('description'),
                        Select::make('team')
                            ->multiple()
                            ->relationship(name: 'team', titleAttribute: 'name'),
                    ]
                )
        ];
    }

    protected function getEditModalFormSchema(string|int|null $recordId): array
    {
        return [
                TextInput::make('title'),
                Textarea::make('description'),                 
        ];
    }

    protected function additionalRecordData(Model $record): Collection
    {
        return collect([
            'urgent' => $record->urgent,
            'progress' => $record->progress,
            'team' => $record->team,
            'description' => $record->description,
        ]);
    }
}
