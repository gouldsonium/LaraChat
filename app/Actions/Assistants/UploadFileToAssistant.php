<?php

namespace App\Actions\Assistants;

use App\Services\ChatGptService;
use App\Models\Assistant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class UploadFileToAssistant
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function __invoke(int $id, string $fullPath)
    {
        try {
            $assistant = Assistant::findOrFail($id);
            $uploadResponse = $this->chatGptService->uploadFile($fullPath, 'assistants');

        } catch (Exception $e) {
            Log::error('Upload file to assistant action error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
}
