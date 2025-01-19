<?php

namespace App\Actions\Assistants;

use App\Services\ChatGptService;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CreateAssistant
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(array $validatedData)
    {
        try {
            // Attempt to create the assistant via the ChatGptService
            $response = $this->chatGptService->createAssistant((object) $validatedData);

            if($response->successful()){
                Assistant::create([
                    'assistant_id' => $response->json()['id'],
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                    'model' => $validatedData['model'],
                    'user_id' => Auth::id(),
                ]);
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to create assistant';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('CreateAssistant action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
