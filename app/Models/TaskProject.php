<?php

namespace App\Models;

use App\Models\User;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskProject extends Model
{

    protected $fillable = [
        'name',
        'description',
        'deadline',
    ];

    public $timestamps = true;

    /**
     * Get all of the comments for the TaskUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_project_user', 'task_project_id', 'user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'task_project_id');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class, 'task_project_id');
    }

}
