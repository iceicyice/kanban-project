<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Status;
use App\Models\TaskProject;
use Illuminate\Database\Seeder;

class ProjectWithTasksSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Create 3 projects
        for ($p = 1; $p <= 3; $p++) {
            $project = TaskProject::create([
                'name'        => "Project {$p}",
                'description' => "This is project {$p}",
                'progress'    => 0, // will update after tasks
                'deadline'    => now()->addDays(rand(30, 90)),
            ]);

            // Attach all users to project
            $project->users()->sync($users->pluck('id'));

            // Create statuses
            $statuses = collect([
                Status::create(['name' => 'Todo',        'task_project_id' => $project->id, 'order_column' => 1]),
                Status::create(['name' => 'In Progress', 'task_project_id' => $project->id, 'order_column' => 2]),
                Status::create(['name' => 'Done',        'task_project_id' => $project->id, 'order_column' => 3]),
            ]);

            $taskProgressValues = [];

            // Create 30 tasks
            for ($t = 1; $t <= 30; $t++) {

                // Build checklist
                $checklist = [
                    ['label' => 'Step A', 'done' => (bool) rand(0, 1)],
                    ['label' => 'Step B', 'done' => (bool) rand(0, 1)],
                    ['label' => 'Step C', 'done' => (bool) rand(0, 1)],
                ];

                // Calculate progress based on checklist
                $total   = count($checklist);
                $done    = collect($checklist)->where('done', true)->count();
                $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

                $task = Task::create([
                    'title'          => "Task {$t} of Project {$p}",
                    'description'    => "This is task {$t} for project {$p}.",
                    'urgent'         => (bool) rand(0, 1),
                    'progress'       => $progress,
                    'note'           => "Some notes for task {$t}.",
                    'tag'            => ['tag' . rand(1, 5), 'tag' . rand(6, 10)],
                    'checklist'      => $checklist,
                    'deadline'       => now()->addDays(rand(5, 60)),
                    'attachment'     => [],
                    'user_id'        => $users->random()->id,
                    'task_project_id'=> $project->id,
                    'status_id'      => $statuses->random()->id,
                    'order_column'   => $t,
                    'color'          => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
                ]);

                $taskProgressValues[] = $progress;
            }

            // ✅ Update project progress as average of all task progress
            $project->update([
                'progress' => (int) round(collect($taskProgressValues)->avg()),
            ]);
        }
    }
}
