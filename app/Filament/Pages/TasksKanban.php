<?php

namespace App\Filament\Pages;
use App\Models\Task;
use App\Models\TaskUser;
use App\Enums\TaskStatus;
use Filament\Pages\Model;
use Filament\Actions\CreateAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Auth;
use App\Forms\Components\RangeSlider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\ColorPicker;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;


class TasksKanban extends KanbanBoard
{

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Tasks';
    protected static string $model = Task::class;
    protected static string $statusEnum = TaskStatus::class;

    protected static string $view = 'tasks.kanban-board';

    protected static string $headerView = 'tasks.kanban-header';

    protected static string $recordView = 'tasks.kanban-record';

    protected static string $statusView = 'tasks.kanban-status';
    protected static ?int $navigationSort = 2;

    public string $search = '';

    public static function getNavigationBadge(): ?string
    {
        return Task::getModel()::count();
    }
    
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of Tasks';
    }

    public function getStatusesProperty(): Collection
    {
        $records = $this->records();

        return collect(TaskStatus::cases())->map(function ($status) use ($records) {
            return [
                'id' => $status->value,
                'title' => $status->name,
                'records' => $records->where('status', $status->value)->values(),
            ];
        });
    }

    protected function records(): Collection
    {

        return Task::ordered()->get();
        // $query = Task::with(['team', 'user'])
        // ->where(function ($query) {
        //     $query->where('user_id', Auth::id()) // task owner
        //           ->orWhereHas('team', function ($teamQuery) {
        //               $teamQuery->where('user_id', Auth::id()); // team owner
        //           });
        // });

        // if ($this->search) {
        //     $query->where(function ($q) {
        //         $q->where('title', 'like', '%' . $this->search . '%')
        //         ->orWhere('description', 'like', '%' . $this->search . '%');
        //     });
        // }

        // return $query->ordered()->get();   

    }

    public function updatedSearch()
    {
        $this->dispatch('$refresh');
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
                        Split::make([
                            Section::make([
                                TextInput::make('title')->required(),
                                Textarea::make('description')->required(),
                                Select::make('team')
                                    ->multiple()
                                    ->relationship(name: 'team', titleAttribute: 'name')->required(),
                            ]),
                            Section::make([
                                Checkbox::make('Urgent'),       
                                ColorPicker::make('color')
                                    ->hexColor(),
                            ])->grow(false),
                        ])->from('sm')
                        
                    ]
                )
        ];
    }

    protected function getEditModalFormSchema(string|int|null $recordId): array
    {
        return [
                TextInput::make('title')->required(),
                Textarea::make('description')->required(),    
                Select::make('team')
                            ->multiple()
                            ->relationship(name: 'team', titleAttribute: 'name')->required(),
                
                RangeSlider::make('progress')
                            ->live()
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
