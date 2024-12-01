<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ChatGptController extends Controller
{
    // Handle the first load (without sending a message)
    public function show()
    {
        // Retrieve the conversation history from the session on first load
        $conversationHistory = session('conversation_history', []);

        return Inertia::render('Chat', [
            'conversationHistory' => $conversationHistory, // Pass conversation history
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $apiKey = env('OPEN_AI_KEY'); // Ensure this is set in your .env file

        // Retrieve the previous conversation from the session
        $conversationHistory = session('conversation_history', []);

        // Add full system message to the conversation if it's the first message
        if (empty($conversationHistory)) {
            $systemMessagePath = storage_path('system_message.txt'); // Path to the file in storage
            $systemMessage = file_get_contents($systemMessagePath);
            $conversationHistory[] = ['role' => 'system', 'content' => $systemMessage];
        }

        // Add the user's current message to the conversation
        $conversationHistory[] = [
            'role' => 'user',
            'content' => $request->message,
        ];

        // Send the conversation history to OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $conversationHistory,
        ]);

        if ($response->successful()) {
            // Get the system's reply
            $reply = $response->json('choices')[0]['message']['content'];

            // Add the system's reply to the conversation
            $conversationHistory[] = [
                'role' => 'system',
                'content' => $reply,
            ];

            // Save the conversation history in the session
            session(['conversation_history' => $conversationHistory]);

            return Inertia::location(url()->previous()); // Redirect without full reload
        }

        return back()->withErrors([
            'error' => 'Failed to fetch response from OpenAI',
        ]);
    }

    public function clearConversation(Request $request)
    {
        $request->session()->forget('conversation_history');
        return redirect()->back();
    }
}
