<?php

namespace App\Actions\Threads;

use App\Services\ChatGptService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\AssistantThread;

/**
 * Create Thread
 */
class CreateThread
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $assistantId)
    {
        try {
            $response = $this->chatGptService->createThread();
            if ($response->successful()) {
                $threadId = $response->json('id');

                $thread = AssistantThread::create([
                    'thread_id' => $threadId,
                    'assistant_id' => $assistantId,
                    'user_id' => Auth::id()
                ]);

                return $thread;
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to create thread';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('CreateThread action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
