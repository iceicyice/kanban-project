<?php

namespace App\Filament\Pages;
use Filament\Panel;
use App\Models\Task;
use App\Models\Status;
use Filament\Pages\Model;
use App\Models\TaskProject;
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
use Filament\Navigation\NavigationItem;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\ColorPicker;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;


class TasksKanban extends KanbanBoard
{

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $title = 'Tasks';
    protected static string $model = Task::class;

    protected static bool $shouldRegisterNavigation = false;

    // protected static ?string $navigationGroup = 'Projects';

    protected static string $view = 'tasks.kanban-board';
    protected static string $headerView = 'tasks.kanban-header';
    protected static string $recordView = 'tasks.kanban-record';
    protected static string $statusView = 'tasks.kanban-status';
    // protected static ?int $navigationSort = 2;

    protected string $editModalTitle = 'Edit Task';
    protected string $editModalWidth = '5xl';
    protected string $editModalSaveButtonLabel = 'Save';
    protected string $editModalCancelButtonLabel = 'Cancel';

    public string $search = '';
    public string $urgent = '';

    public TaskProject $project;
    protected static ?string $slug = 'tasks-kanban';
    

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $parameters['project'] = $parameters['project'] ?? request()->route('project');
        return route('filament.pages.tasks-kanban', $parameters, $isAbsolute);
    }

    public function mount(): void
    {
        $this->form->fill();
        
        $projectId = request()->query('project');
        $this->project = TaskProject::findOrFail($projectId);

        // Check if the logged-in user is part of this project
        $isMember = $this->project->users()->where('users.id', Auth::id())->exists();

        if (! $isMember) {
            abort(403, 'You are not authorized to view this project.');
        }
    }
    

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    } 

    protected function statuses(): Collection
    {
            return Status::where('task_project_id', $this->project->id)
                ->orderBy('order_column')
                ->get()
                ->map(fn ($status) => [
                    'id' => $status->id,
                    'title' => $status->name,
            ]);
    }

    public function getStatusesProperty(): Collection
    {
        $records = $this->records();

        return Status::where('task_project_id', $this->project->id)
            ->orderBy('order_column')
            ->get()
            ->map(function ($status) use ($records){
                return [
                    'id' => $status->id,
                    'title' => $status->name,
                    'records' => $records->where('status_id', $status->id)->values(),
                ];
            });
    }

    protected function records(): Collection
    {

        $query = Task::query()
            ->where('task_project_id', $this->project->id);

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
                    $data['task_project_id'] = $this->project->id;
                    $data['user_id'] = auth()->id();
                    $defaultStatus = Status::where('task_project_id', $this->project->id)
                    ->orderBy('order_column')
                    ->first();
                    if ($defaultStatus) {
                        $data['status_id'] = $defaultStatus->id;
                    }
                    return $data;
                })
                ->model(Task::class)
                ->form(
                    [   
                        Split::make([
                            Section::make([
                                TextInput::make('title')->required(),
                                Textarea::make('description')->required(),
                                RichEditor::make('note'),
                                FileUpload::make('attachment')
                                    ->directory('task-attachments')
                                    ->visibility('private')
                                    ->preserveFilenames()
                                    ->multiple()
                                    ->reorderable()
                                    ->openable(),
                            ]),
                            Section::make([
                                Checkbox::make('urgent'),
                                DatePicker::make('deadline'),
                                ColorPicker::make('color')
                                    ->hexColor()
                                    ->required(),
                                TagsInput::make('tag')
                                    ->label('Tags'),
                            ])->grow(true),
                        ])->from('xs'),
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
                                RichEditor::make('note'),
                                FileUpload::make('attachment')
                                    ->directory('task-attachments')
                                    ->visibility('private')
                                    ->preserveFilenames()
                                    ->multiple()
                                    ->reorderable()
                                    ->openable(),
                            ]),
                            Section::make([
                                Checkbox::make('urgent'),
                                DatePicker::make('deadline'),
                                ColorPicker::make('color')
                                    ->hexColor()
                                    ->required(),
                                TagsInput::make('tag')
                                    ->label('Tags'),
                                RangeSlider::make('progress')
                                    ->live(),
                            ])->grow(true),
                        ])->from('xs'),
                ];
    }


    protected function additionalRecordData(Task $record): Collection
    {
        return collect([
            'urgent' => $record->urgent,
            'progress' => $record->progress,
            'description' => $record->description,
            'tag' => $record->tag,
            'deadline' => $record->deadline,
        ]);
    }
    
    

}
