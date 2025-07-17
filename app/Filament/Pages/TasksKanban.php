<?php

namespace App\Filament\Pages;
use Filament\Panel;
use App\Models\Task;
use App\Models\Status;
use App\Models\TaskUser;
use App\Enums\TaskStatus;
use Filament\Pages\Model;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Navigation\MenuItem;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Split;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use App\Forms\Components\RangeSlider;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;


class TasksKanban extends KanbanBoard
{

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Tasks';
    protected static string $model = Task::class;

    protected static string $view = 'tasks.kanban-board';

    protected static string $headerView = 'tasks.kanban-header';

    protected static string $recordView = 'tasks.kanban-record';

    protected static string $statusView = 'tasks.kanban-status';
    protected static ?int $navigationSort = 2;

    protected string $editModalTitle = 'Edit Task';

    protected string $editModalWidth = '4xl';

    protected string $editModalSaveButtonLabel = 'Save';

    protected string $editModalCancelButtonLabel = 'Cancel';
    

    public string $search = '';

    public string $urgent = '';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public static function getNavigationBadge(): ?string
    {
        $userId = Auth::id();

        return Task::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereHas('team', function ($teamQuery) use ($userId) {
                    $teamQuery->where('user_id', $userId);
                });
        })->count();
    }
    
    public static function getNavigationBadgeTooltip(): ?string
    {
        $userId = Auth::id();

        return Task::where(function ($query) use ($userId) {
            $query->where('urgent', 1)
                    ->where('user_id', $userId)
                    ->orWhereHas('team', function ($teamQuery) use ($userId) {
                        $teamQuery->where('user_id', $userId);
                    });
            })->count(). " Urgent Tasks";
    }

    // public function getStatusesProperty(): Collection
    // {
    //     $records = $this->records();

    //     return collect(TaskStatus::cases())->map(function ($status) use ($records) {
    //         return [
    //             'id' => $status->value,
    //             'title' => $status->name,
    //             'records' => $records->where('status', $status->value)->values(),
    //         ];
    //     });
    // }

    protected function statuses(): Collection
    {
            return Status::orderBy('order_column')->get()->map(function ($status) {
                return [
                    'id' => $status->id,
                    'title' => $status->name,
                ];
            });
    }

    public function getStatusesProperty(): Collection
    {
        $records = $this->records();

        return Status::orderBy('order_column')->get()->map(function ($status) use ($records) {
            return [
                'id' => $status->id,
                'title' => $status->name,
                'records' => $records->where('status_id', $status->id)->values(),
            ];
        });
    }

    protected function records(): Collection
    {

        // $query = Task::query();
        $query = Task::with(['team', 'user'])
        ->where(function ($query) {
            $query->where('user_id', Auth::id()) // task owner
                  ->orWhereHas('team', function ($teamQuery) {
                      $teamQuery->where('user_id', Auth::id()); // team
                  });
        });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->urgent === 'checked') {
            $query->where('urgent', 1); 
        }

        return $query->ordered()->get();
    }

    public function updatedSearch()
    {
        $this->dispatch('$refresh');
    }

    public function onStatusChanged(string|int $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Task::find($recordId)->update(['status_id' => $status]);
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
                                RichEditor::make('note')
                            ]),
                            Section::make([
                                Checkbox::make('urgent'),       
                                ColorPicker::make('color')
                                    ->hexColor()
                                    ->required(),
                            ])->grow(false),
                        ])->from('sm')
                        
                    ]
                ),
                Action::make('toggleUrgent')
                    ->label(fn () => $this->urgent === 'checked' ? 'Show All Tasks' : 'Show Urgent Only')
                    ->icon('heroicon-o-clock')
                    ->color(fn () => $this->urgent === 'checked' ? 'gray' : 'danger')
                    ->action(function () {
                        $this->urgent = $this->urgent === 'checked' ? '' : 'checked';
                        $this->dispatch('$refresh'); // rerender the board
                    })
        ];
    }

    protected function getEditModalFormSchema(string|int|null $recordId): array
    {
        return [
                Split::make([
                            Section::make([
                                TextInput::make('title')->required(),
                                Textarea::make('description')->required(),
                                Select::make('team')
                                    ->multiple()
                                    ->relationship(name: 'team', titleAttribute: 'name')->required(),
                                RichEditor::make('note'),
                            ]),
                            Section::make([
                                Checkbox::make('urgent'),       
                                ColorPicker::make('color')
                                    ->hexColor()
                                    ->required(),
                                RangeSlider::make('progress')
                                    ->live(),
                            ])->grow(false),
                        ])->from('md'),
                
                
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
