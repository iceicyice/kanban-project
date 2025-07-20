<?php

namespace App\Models;

use App\Models\Task;
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
        'order_column'
    ];

    protected static function booted()
    {
        // When creating a new status
        static::creating(function (Status $status) {
            static::where('order_column', '>=', $status->order_column)
                ->increment('order_column');
        });

        // When updating an existing status order
        static::updating(function (Status $status) {
            if ($status->isDirty('order_column')) {
                $oldOrder = $status->getOriginal('order_column');
                $newOrder = $status->order_column;

                if ($newOrder < $oldOrder) {
                    // Move up: shift down all between new and old
                    static::where('id', '!=', $status->id)
                        ->whereBetween('order_column', [$newOrder, $oldOrder - 1])
                        ->increment('order_column');
                } elseif ($newOrder > $oldOrder) {
                    // Move down: shift up all between old and new
                    static::where('id', '!=', $status->id)
                        ->whereBetween('order_column', [$oldOrder + 1, $newOrder])
                        ->decrement('order_column');
                }
            }
        });
    }

     public static function normalizeOrder(): void
    {
        $statuses = static::orderBy('order_column')->get();

        foreach ($statuses as $index => $status) {
            $status->updateQuietly(['order_column' => $index + 1]);
        }
    }


    public function getKanbanStatusTitle(): string
    {
        return $this->name;
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

}
