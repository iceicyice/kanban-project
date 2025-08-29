<?php

namespace App\Providers;

use App\Models\User;
use Livewire\Livewire;
use App\Policies\UserPolicy;
use Filament\Support\Assets\Js;
use App\Notifications\TaskCommented;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use App\Filament\Widgets\Notifications;
use Illuminate\Support\ServiceProvider;
use Coolsam\NestedComments\Models\Comment;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        Gate::policy(User::class, UserPolicy::class);

        Comment::created(function ($comment) {
            if ($comment->commentable_type === \App\Models\Task::class) {
                $task = $comment->commentable;
                $projectUsers = $task->project->users;

                $projectUsers->where('id', '!=', $comment->user_id)
                            ->each(fn($user) => $user->notify(new TaskCommented($task, $comment)));
            }
        });

    }
}
