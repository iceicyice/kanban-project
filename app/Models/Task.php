<?php

namespace App\Models;

use App\Models\Status;
use App\Models\TaskProject;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Task extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory, SortableTrait;

    protected $fillable = [
        'title',
        'description',
        'urgent',
        'progress',
        'note',
        'tag',
        'attachment',
        'checklist',
        'deadline',
        'color',
        'status_id',
        'user_id',
        'task_project_id',
        'order_column'
    ];

    protected $guarded = [];

    public static function ignoreTimestamps($should = true)
    {
        if($should){
            static::$ignoreTimestampsOn = array_values(array_merge(static::$ignoreTimestampsOn, [static::class]));
        }else{
            static::$ignoreTimestampsOn = array_values(array_diff(static::$ignoreTimestampsOn, [static::class]));
        }
    }

    /**
     * The roles that belong to the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(TaskProject::class, 'task_project_id');
    }

    public function user(): BelongsTo
    { 
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($task) {
            $task->team()->detach(); // or delete if it's a related model
        });
    }

    protected static function booted()
    {
        static::saved(fn ($tasks) => $tasks->project->updateProgress());
        static::deleted(fn ($tasks) => $tasks->project->updateProgress());
    }

    protected $casts = [
        'urgent' => 'boolean',
        'tag' => 'array',
        'checklist' => 'array',
        'attachment' => 'array',
        'deadline' => 'datetime',
        'progress' => 'integer',
    ];
    
    public function statuses()
    {
        return $this->belongsToMany(Status::class, 'task_status');
    }
}
