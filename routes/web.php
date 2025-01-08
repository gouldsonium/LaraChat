<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatGptController;
use App\Http\Controllers\AssistantsController;
use App\Http\Controllers\CompletionsController;

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
    Route::get('/dashboard', function () {
        $conversationHistory = session('conversation_history', []);
        return Inertia::render('Dashboard', [
            'conversationHistory' => $conversationHistory,
        ]);
    })->name('dashboard');

    // Completions routes
    Route::controller(CompletionsController::class)->prefix('completions')->group(function () {
        Route::get('/', 'show')->name('completions.show');
        Route::post('/create', 'create')->name('completions.create');
        Route::get('/manage/{id}', 'manage')->name('completions.manage');
        Route::put('/update/{id}', 'update')->name('completions.update');
        Route::delete('/delete/{id}', 'delete')->name('completions.delete');
    });

    // Assistants routes
    Route::controller(AssistantsController::class)->prefix('assistants')->group(function () {
        Route::get('/', 'show')->name('assistants.show');
        Route::post('/create', 'create')->name('assistants.create');
        Route::get('/manage/{id}', 'manage')->name('assistants.manage');
        Route::put('/update/{id}', 'update')->name('assistants.update');
        Route::delete('/delete/{id}', 'delete')->name('assistants.delete');
    });
});
