<?php

use App\Filament\Pages\TasksKanban;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect('/admin');
// });

// Route::middleware(['web', 'auth'])
//     ->prefix('admin')
//     ->group(function () {
//         Route::get('/tasks-kanban/{project}', TasksKanban::class)
//             ->name('filament.admin.pages.tasks-kanban');
//     });

Route::post('/notifications/{id}/read', function ($id) {
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->markAsRead();
    return back();
})->name('notifications.read');
