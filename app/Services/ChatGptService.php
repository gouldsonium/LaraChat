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
            throw new Exception('Failed to create completions.');
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
            Log::error("Create assistant error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get assistant using the OpenAI API.
     */
    public function getAssistant(string $assistantID)
    {
        try {
            return Http::withHeaders($this->getHeaders())->get('https://api.openai.com/v1/assistants/' . $assistantID);
        } catch (Exception $e) {
            Log::error("Get Assistant error: " . $e->getMessage());
            throw new Exception('Failed to get assistant details.');
        }
    }

    /**
     * Update assistant using the OpenAI API.
     */
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
            Log::error("Update Assistant error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete assistant using the OpenAI API.
     */
    public function deleteAssistant(string $assistantID)
    {
        try {
            return Http::withHeaders($this->getHeaders())->delete('https://api.openai.com/v1/assistants/' . $assistantID);
        } catch (Exception $e) {
            Log::error("Delete Assistant error: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Create thread using the OpenAI API.
     */
    public function createThread()
    {
        try {
            return Http::withHeaders($this->getHeaders())->post('https://api.openai.com/v1/threads');
        } catch (Exception $e) {
            Log::error("Create Thread error: " . $e->getMessage());
            throw new Exception('Failed to create thread.');
        }
    }

    /**
     * Get thread messages using the OpenAI API.
     */
    public function getThreadMessages(string $threadId)
    {
        try {
            return Http::withHeaders($this->getHeaders())->get('https://api.openai.com/v1/threads/' . $threadId . '/messages');
        } catch (Exception $e) {
            Log::error("Fetch Thread error: " . $e->getMessage());
            throw new Exception('Failed to fetch thread.');
        }
    }

    /**
     * Create message on thread using the OpenAI API.
     */
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
            throw new Exception('Failed to create message in thread.');
        }
    }

    /**
     * Delete thread using the OpenAI API.
     */
    public function deleteThread(string $threadId)
    {
        try {
            return Http::withHeaders($this->getHeaders())->delete('https://api.openai.com/v1/threads/' . $threadId);
        } catch (Exception $e) {
            Log::error("Delete Thread error: " . $e->getMessage());
            throw new Exception('Failed to delete thread.');
        }
    }

    /**
     * Run thread using the OpenAI API and stream so response is generated when run is complete.
     */
    public function createRunWithStream(string $threadId, string $assistantId)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(
                "https://api.openai.com/v1/threads/{$threadId}/runs",
                [
                    'headers' => $this->getHeaders(),
                    'json' => [
                        'assistant_id' => $assistantId,
                        'stream' => true,
                    ],
                    'stream' => true,
                ]
            );

            $body = $response->getBody();
            $buffer = ''; // To accumulate incomplete JSON fragments
            $completeMessage = ''; // Accumulated assistant message
            $messageComplete = false; // Track message completion

            while (!$body->eof()) {
                $chunk = $body->read(1024);

                if (empty(trim($chunk))) {
                    continue; // Skip empty chunks
                }

                $buffer .= $chunk; // Add chunk to buffer

                // Process each complete line in the buffer
                $lines = explode("\n", $buffer);
                $buffer = array_pop($lines); // Keep the last (potentially incomplete) line in the buffer

                foreach ($lines as $line) {
                    $line = trim($line);

                    // Handle `[DONE]` event
                    if ($line === 'data: [DONE]') {
                        $messageComplete = true;
                        break; // Exit loop on completion
                    }

                    // Skip lines that don't start with "data:"
                    if (!str_starts_with($line, 'data:')) {
                        continue;
                    }

                    // Extract JSON content from "data:"
                    $jsonData = substr($line, 5);

                    // Attempt to decode the JSON data
                    $data = json_decode($jsonData, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning("Invalid JSON in chunk: {$jsonData}");
                        continue;
                    }

                    // Process relevant events
                    switch ($data['object'] ?? null) {
                        case 'thread.message.delta':
                            // Accumulate delta content
                            foreach ($data['delta']['content'] as $content) {
                                $completeMessage .= $content['text']['value'] ?? '';
                            }
                            break;

                        case 'thread.message.completed':
                            $messageComplete = true;
                            break;

                        default:
                            // Ignore other events
                            break;
                    }
                }

                if ($messageComplete) {
                    break;
                }
            }

            if (!$messageComplete) {
                throw new \Exception('Stream ended without receiving a complete response.');
            }

            return $completeMessage; // Return the full message
        } catch (\Exception $e) {
            Log::error("Run Thread with Stream error: " . $e->getMessage());
            throw new \Exception('Failed to run thread with streaming. Please try again later.');
        }
    }
}
