<?php

namespace App\Models;

use App\Models\Task;
use App\Models\TaskProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'slug',
        'order_column',
        'task_project_id'
    ];

    protected static function booted()
    {
        static::creating(function (Status $status) {
            if (is_null($status->order_column)) {
                $status->order_column = static::where('task_project_id', $status->task_project_id)
                    ->max('order_column') + 1;
            } else {
                static::where('task_project_id', $status->task_project_id)
                    ->where('order_column', '>=', $status->order_column)
                    ->increment('order_column');
            }
        });

        static::updating(function (Status $status) {
            if ($status->isDirty('order_column')) {
                $oldOrder = $status->getOriginal('order_column');
                $newOrder = $status->order_column;

                // Ensure both old and new order are integers
                if (!is_numeric($oldOrder) || !is_numeric($newOrder)) {
                    return;
                }

                if ($newOrder < $oldOrder) {
                    // Move up: shift others down
                    static::where('task_project_id', $status->task_project_id)
                        ->where('id', '!=', $status->id)
                        ->whereBetween('order_column', [(int)$newOrder, (int)$oldOrder - 1])
                        ->increment('order_column');
                } elseif ($newOrder > $oldOrder) {
                    // Move down: shift others up
                    static::where('task_project_id', $status->task_project_id)
                        ->where('id', '!=', $status->id)
                        ->whereBetween('order_column', [(int)$oldOrder + 1, (int)$newOrder])
                        ->decrement('order_column');
                }
            }
        });
    }

    public static function normalizeOrder(int $projectId): void
    {
        $statuses = static::where('task_project_id', $projectId)
            ->orderBy('order_column')
            ->get();

        foreach ($statuses as $index => $status) {
            $status->updateQuietly(['order_column' => $index + 1]);
        }
    }


    public function getKanbanStatusTitle(): string
    {
        return $this->name;
    }

    public function projects(): BelongsTo
    {
        return $this->belongsTo(TaskProject::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_status');
    }

}
