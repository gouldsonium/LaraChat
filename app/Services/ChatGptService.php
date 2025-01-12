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

    public function createThread()
    {
        try {
            return Http::withHeaders($this->getHeaders())->post('https://api.openai.com/v1/threads');
        } catch (Exception $e) {
            Log::error("Create Thread error: " . $e->getMessage());
            throw new Exception('Failed to create thread. Please try again later.');
        }
    }

    public function getThreadMessages(string $threadId)
    {
        try {
            return Http::withHeaders($this->getHeaders())->get('https://api.openai.com/v1/threads/' . $threadId . '/messages');
        } catch (Exception $e) {
            Log::error("Fetch Thread error: " . $e->getMessage());
            throw new Exception('Failed to fetch thread. Please try again later.');
        }
    }

    public function createThreadMessage(string $threadId, string $message)
    {
        try {
            return Http::withHeaders($this->getHeaders())
                ->post('https://api.openai.com/v1/threads/' . $threadId . '/messages', [
                    'role' => 'user',
                    'content' => $message
                ]);
        } catch (Exception $e) {
            Log::error("Create Thread Message Error: " . $e->getMessage());
            throw new Exception('Failed to create message in thread. Please try again later.');
        }
    }

    public function createRun(string $threadId, string $assistantID)
    {
        try {
            return Http::withHeaders($this->getHeaders())
                ->post('https://api.openai.com/v1/threads/' . $threadId . '/runs', [
                    'assistant_id' => $assistantID,
                ]);
        } catch (Exception $e) {
            Log::error("Run Thread error: " . $e->getMessage());
            throw new Exception('Failed to run thread. Please try again later.');
        }
    }

    public function deleteThread(string $threadId)
    {
        try {
            return Http::withHeaders($this->getHeaders())->delete('https://api.openai.com/v1/threads/' . $threadId);
        } catch (Exception $e) {
            Log::error("Delete Thread error: " . $e->getMessage());
            throw new Exception('Failed to delete thread. Please try again later.');
        }
    }
    public function pollRunStatus(string $threadId, string $runId): string
    {
        $timeout = 5; // Maximum time to wait for the run to complete (in seconds)
        $interval = 1; // Interval between status checks (in seconds)

        // Poll the status until it is completed or timeout occurs
        $start = time();
        while (time() - $start < $timeout) {
            // Make the API request to get the status of the run
            $response = Http::withHeaders($this->getHeaders())
                ->get("https://api.openai.com/v1/threads/{$threadId}/runs/{$runId}");

            if ($response->successful()) {
                $status = $response->json()['status'];  // Check the status from the response

                if ($status === 'completed') {
                    return 'completed';
                }

                // If it's still queued or processing, wait for the next interval
                sleep($interval);
            } else {
                // Handle any errors in the API response (e.g., invalid status)
                Log::error('Error fetching run status', [
                    'thread_id' => $threadId,
                    'run_id' => $runId,
                    'error' => $response->body(),
                ]);
                break;
            }
        }

        return 'timeout';  // If we reach the timeout, return 'timeout'
    }
}
