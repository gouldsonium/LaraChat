<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatGptService
{
    public function createCompletions(array $conversationHistory, string $model, string $user = null)
    {
        try {
            $apiKey = config('chat-gpt.key'); // Ensure this is set in your .env file

            // OpenAI API request
            return Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $conversationHistory,
                // 'store' => true,
                // 'metadata' => [
                //     'user' => $user
                // ]
            ]);
        } catch (\Exception $e) {
            // Log error if needed
            Log::error("Chat processing error: " . $e->getMessage());

            // Return JSON response with error message
            return response()->json(['error' => "Oops! Something went wrong. Please try again later."], 500);
        }
    }
}
