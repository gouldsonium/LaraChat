<?php

namespace App\Actions\Assistants;

use App\Services\ChatGptService;
use App\Models\Assistant;
use Exception;
use Illuminate\Support\Facades\Log;

class GetAssistant
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $id)
    {
        try {
            $assistant = Assistant::findOrFail($id);
            $response = $this->chatGptService->getAssistant($assistant->assistant_id);

            if ($response->successful()) {
                $jsonResponse = $response->json();
                return array_merge($assistant->toArray(), [
                    'instructions' => $jsonResponse['instructions'],
                    'tools' => array_column($jsonResponse['tools'], 'type'),
                ]);
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to get assistant';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('Get Assistant action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
