<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    /**
     * Get all of the comments for the TaskUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function task_user(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
