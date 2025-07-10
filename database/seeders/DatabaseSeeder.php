<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $admin = User::factory()->create([
            'name' => 'ice',
            'email' => 'ice@gmail.com',
        ]);

        User::factory()->create([
            'name' => 'ice2',
            'email' => 'ice2@gmail.com',
        ]);

        $users = User::factory(10)->create();

        $tasks = Task::factory(30)
        ->recycle($users)
        ->create();

        $tasks->each(function (Task $task) use ($users)
        {
            $task->team()->attach($users->shuffle()->take(fake()->numberBetween(1, 4))->pluck('id'));
        });

        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleUsers = Role::create(['name' => 'User']);
        $permission = Permission::create(['name' => 'See User Table']);
        $permission->assignRole($roleAdmin);
        $admin->assignRole($roleAdmin);
        // $users->assignRole($roleUsers);
    }
}
