<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class ChatGptController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('OPEN_AI_KEY'); // Ensure this is set in your .env file

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->withoutVerifying()->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->message],
            ],
            'store' => true
        ]);

        if ($response->successful()) {
            $reply = $response->json('choices')[0]['message']['content'];

            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'reply' => $reply,
            ]);
        }

        return back()->withErrors(['error' => 'Failed to fetch response from OpenAI']);
    }
}
