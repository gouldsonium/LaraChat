<?php

use App\Http\Controllers\AssistantMessagesController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatGptController;
use App\Http\Controllers\AssistantsController;
use App\Http\Controllers\CompletionsController;
use App\Http\Controllers\DashboardController;

Route::get('/', [ChatGptController::class, 'show'])->name('chat.show');
Route::post('/', [ChatGptController::class, 'send'])->name('chat.send');
Route::delete('/clear', [ChatGptController::class, 'clearConversation'])->name('chat.clear');

// Group routes that share the same middleware
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Completions routes
    Route::controller(CompletionsController::class)->prefix('completions')->group(function () {
        Route::get('/', 'show')->name('completions.show');
        Route::post('/create', 'create')->name('completions.create');
        Route::get('/manage/{id}', 'manage')->name('completions.manage');
        Route::put('/update/{id}', 'update')->name('completions.update');
        Route::delete('/delete/{id}', 'delete')->name('completions.delete');

        Route::get('/chat/{id}', 'chat')->name('completions.chat');
        Route::post('/send/{id}', 'send')->name('completions.send');
        Route::delete('/clear/{id}', 'clearConversation')->name('completions.clear');
    });

    // Assistants routes
    Route::controller(AssistantsController::class)->prefix('assistants')->group(function () {
        Route::get('/', 'show')->name('assistants.show');
        Route::post('/create', 'create')->name('assistants.create');
        Route::get('/manage/{id}', 'manage')->name('assistants.manage');
        Route::put('/update/{id}', 'update')->name('assistants.update');
        Route::delete('/delete/{id}', 'delete')->name('assistants.delete');

    });

    Route::controller(AssistantMessagesController::class)->prefix('assistants')->group(function () {
        Route::get('/chat/{id}', 'chat')->name('assistants.chat');
        Route::post('/send', 'send')->name('assistants.send');
        Route::delete('/clear/{id}', 'deleteThread')->name('assistants.clear');
    });
});
