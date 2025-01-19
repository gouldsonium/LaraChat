<?php

namespace App\Actions\Threads;

use App\Services\ChatGptService;
use Illuminate\Support\Facades\Log;
use Exception;

class SendThreadMessage
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(string $threadId, string $assistantId, string $message)
    {
        try {
            // Step 1: Create the message in the thread
            $createMessageResponse = $this->chatGptService->createThreadMessage($threadId, $message);
            if (!$createMessageResponse->successful()) {
                $errorDetails = $createMessageResponse->json()['error']['message'] ?? 'Failed to create thread message';
                throw new Exception($errorDetails);
            }

            // Step 2: Run the thread and process the response stream
            $assistantResponse = $this->chatGptService->createRunWithStream($threadId, $assistantId);

            return $assistantResponse;
        } catch (\Exception $e) {
            Log::error('SendThreadMessage action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
