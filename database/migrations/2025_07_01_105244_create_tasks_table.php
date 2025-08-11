<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('task_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->tinyInteger('progress')->default(0);
            $table->timestamps();
            $table->date('deadline')->nullable();
        });

        Schema::create('task_project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });

        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('order_column')->default(1);
            $table->foreignId('task_project_id')
                ->constrained('task_projects')
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('urgent')->default(false);
            $table->tinyInteger('progress')->default(0);
            $table->text('note')->nullable();
            $table->text('tag')->nullable();
            $table->json('checklist')->nullable();
            $table->date('deadline')->nullable();
            $table->text('attachment')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('task_project_id')->constrained();
            $table->foreignId('status_id')->default(1)->constrained('statuses');
            $table->unsignedInteger('order_column');
            $table->text('color')->nullable();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
