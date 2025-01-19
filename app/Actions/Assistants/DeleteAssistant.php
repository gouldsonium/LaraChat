<?php

namespace App\Actions\Assistants;

use App\Services\ChatGptService;
use App\Models\Assistant;
use Illuminate\Support\Facades\Log;
use Exception;

class DeleteAssistant
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $id)
    {
        try {
            $assistant = Assistant::findOrFail($id);
            $response = $this->chatGptService->deleteAssistant($assistant->assistant_id);

            if ($response->successful()) {
                $assistant->delete();
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to delete assistant';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('Delete Assistant action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
