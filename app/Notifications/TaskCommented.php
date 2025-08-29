<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Task;
use Coolsam\NestedComments\Models\Comment;

class TaskCommented extends Notification
{
    use Queueable;

    protected Task $task;
    protected Comment $comment;

    public function __construct(Task $task, Comment $comment)
    {
        $this->task = $task;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'task_id'      => $this->task->id,
            'task_title'   => $this->task->title,
            'project_id'   => $this->task->project->id,
            'project_name' => $this->task->project->name,
            'comment_id'   => $this->comment->id,
            'comment_body' => $this->comment->body,
            'commented_by' => $this->comment->user->name ?? 'Guest',
            'commenter_avatar' => $this->comment->user ? $this->comment->user->getFilamentAvatarUrl() : null,
        ];
    }

}
