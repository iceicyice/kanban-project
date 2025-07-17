<?php

namespace App\Models;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Status extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'slug',
        'order_column'
    ];

    public function getKanbanStatusId(): string|int
    {
        return $this->slug ?? $this->id;
    }

    public function getKanbanStatusTitle(): string
    {
        return $this->name;
    }

    public function task(): BelongsTo
    { 
        return $this->belongsTo(Task::class);
    }
}
