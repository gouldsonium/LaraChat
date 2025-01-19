<?php

namespace App\Actions\Assistants;

use App\Services\ChatGptService;
use App\Models\Assistant;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class UpdateAssistant
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $id, array $validatedData)
    {
        try {
            $assistant = Assistant::findOrFail($id);
            $response = $this->chatGptService->updateAssistant($assistant->assistant_id, (object) $validatedData);

            if ($response->successful()) {
                $assistant->update([
                    'model' => $validatedData['model'],
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                ]);
            } else {
                $errorDetails = $response->json()['error']['message'] ?? 'Failed to update assistant';
                throw new Exception($errorDetails);
            }
        } catch (Exception $e) {
            Log::error('Update Assistant action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

}
