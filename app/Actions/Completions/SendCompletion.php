<?php

namespace App\Actions\Completions;

use App\Models\Completion;
use App\Services\ChatGptService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Send Completion
 */
class SendCompletion
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $id, string $message)
    {
        try {
            $completion = Completion::findOrFail($id);
            $conversationHistory = session('conversation_history_' . $id, []);

            $conversationHistory[] = [
                'role' => 'user',
                'content' => $message,
            ];

            $response = $this->chatGptService->createCompletions($conversationHistory, $completion->model);

            if ($response->successful()) {
                // Get the system's reply
                $reply = $response->json('choices')[0]['message']['content'];

                $conversationHistory[] = [
                    'role' => 'system',
                    'content' => $reply,
                ];

                // Save the conversation history in the session
                session(['conversation_history_' . $id => $conversationHistory]);
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to send completion';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('Send Completion action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
