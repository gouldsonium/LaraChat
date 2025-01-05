<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChatGptController;
use App\Http\Controllers\AssistantsController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    ])->group(function () {
        Route::get('/dashboard', function () {
            $conversationHistory = session('conversation_history', []);
            return Inertia::render('Dashboard', [
                'conversationHistory' => $conversationHistory
            ]);
        })->name('dashboard');
    });

Route::get('/', [ChatGptController::class, 'show'])->name('chat.show');
Route::post('/', [ChatGptController::class, 'send'])->name('chat.send');
Route::delete('/clear', [ChatGptController::class, 'clearConversation'])->name('chat.clear');

Route::controller(AssistantsController::class)->prefix('assistants')->group(function () {
    Route::get('/', 'show')->name('assistants.show');
    Route::post('/create', 'create')->name('assistants.create');
});
