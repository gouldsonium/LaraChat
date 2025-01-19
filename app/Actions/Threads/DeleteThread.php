<?php

namespace App\Actions\Threads;

use App\Services\ChatGptService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Delete Thread
 */
class DeleteThread
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $assistantId)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $thread = $user->getThreadByAssistantId($assistantId);

            $response = $this->chatGptService->deleteThread($thread->thread_id);

            if($response->successful()){
                $thread->delete();
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to delete thread';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('DeleteThread action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
