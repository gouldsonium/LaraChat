<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ChatGptService;

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

    public function send(Request $request, ChatGptService $chatGptService)
    {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required'
        ]);

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
        $response = $chatGptService->createCompletions($conversationHistory, $request->model);

        if ($response->successful()) {
            // Get the system's reply
            $reply = $response->json('choices')[0]['message']['content'];

            if($request->paid){
                $usage = $response->json('usage'); // Assuming 'usage' includes 'prompt_tokens' and 'completion_tokens'

                // Define pricing for input and output tokens based on the model
                $pricingConfig = config('chat-gpt.models');

                // Get the model's pricing or fall back to a default pricing
                $pricing = $pricingConfig[$request->model] ?? ['input' => 0.0000015, 'output' => 0.000002];

                // Calculate the total cost
                $inputCost = ($usage['prompt_tokens'] ?? 0) * $pricing['input'];
                $outputCost = ($usage['completion_tokens'] ?? 0) * $pricing['output'];
                $totalCost = $inputCost + $outputCost;

                /** @var \App\Models\User $user */
                $user = Auth::user();
                // Check if the user has sufficient balance
                if ($user->balance < $totalCost) {
                    return back()->withErrors([
                        'error' => 'Insufficient balance to process your request.',
                    ]);
                };

                // Deduct the cost from the user's balance
                $user->balance -= $totalCost;
                $user->save();
            }

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
