<?php

namespace App\Actions\Threads;

use App\Services\ChatGptService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Get Thread Messages
 */
class GetThreadMessages
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(string $threadId)
    {
        try {
            $response = $this->chatGptService->getThreadMessages($threadId);

            if ($response->successful()) {
                return $this->transformMessagesToConversationHistory($response->json());
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to get thread messages';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('GetThreadMessages action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    protected function transformMessagesToConversationHistory(array $messages): array
    {
        $conversationHistory = [];

        // Reverse the messages data array
        $messagesData = array_reverse($messages['data']);

        foreach ($messagesData as $message) {
            // Extract the role
            $role = $message['role'];

            // Extract the content, handling the content array
            $content = collect($message['content'])
                ->pluck('text.value')
                ->implode(' '); // Join multiple content parts into a single string if necessary

            // Add the transformed message to the conversation history
            $conversationHistory[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        return $conversationHistory;
    }
}
