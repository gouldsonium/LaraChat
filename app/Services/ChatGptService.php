<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ChatGptService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('chat-gpt.key');

        if (!$this->apiKey) {
            throw new Exception('ChatGPT API key is not configured in the environment.');
        }
    }

    /**
     * Get default headers for API requests.
     */
    protected function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2'
        ];
    }

    /**
     * Create completions using the OpenAI API.
     */
    public function createCompletions(array $conversationHistory, string $model, string $user = null)
    {
        try {
            return Http::withHeaders($this->getHeaders())
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $conversationHistory,
                ]);
        } catch (Exception $e) {
            Log::error("Chat processing error: " . $e->getMessage());
            throw new Exception('Failed to create completions. Please try again later.');
        }
    }

    /**
     * Create an assistant using the OpenAI API.
     */
    public function createAssistant(object $data)
    {
        try {
            return Http::withHeaders($this->getHeaders())
                ->post('https://api.openai.com/v1/assistants', [
                    'model' => $data->model,
                    'name' => $data->name,
                    'description' => $data->description,
                    'instructions' => $data->instructions,
                    'tools' => $data->tools,
                ]);
        } catch (Exception $e) {
            Log::error("Assistant creation error: " . $e->getMessage());
            throw new Exception('Failed to create assistant. Please try again later.');
        }
    }

    public function getAssistant(string $assistantID)
    {
        try {
            return Http::withHeaders($this->getHeaders())->get('https://api.openai.com/v1/assistants/' . $assistantID);
        } catch (Exception $e) {
            Log::error("Get Assistant error: " . $e->getMessage());
            throw new Exception('Failed to get assistant details. Please try again later.');
        }
    }

    public function updateAssistant(string $assistantID, $data)
    {
        try {
            return Http::withHeaders($this->getHeaders())
                ->post('https://api.openai.com/v1/assistants/' . $assistantID, [
                    'model' => $data->model,
                    'name' => $data->name,
                    'description' => $data->description,
                    'instructions' => $data->instructions,
                    'tools' => $data->tools,
                ]);
        } catch (Exception $e) {
            Log::error("Assistant update error: " . $e->getMessage());
            throw new Exception('Failed to update assistant. Please try again later.');
        }
    }

    public function deleteAssistant(string $assistantID)
    {
        try {
            return Http::withHeaders($this->getHeaders())->delete('https://api.openai.com/v1/assistants/' . $assistantID);
        } catch (Exception $e) {
            Log::error("Delte Assistant error: " . $e->getMessage());
            throw new Exception('Failed to delete assistant. Please try again later.');
        }
    }
}
