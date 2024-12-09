<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ChatGptService;

class ChatGptTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_create_chat_completions(): void
    {
        $chatGptService = new ChatGptService();
        $conversationHistory[] = ['role' => 'user', 'content' => 'test'];
        $response = $chatGptService->createCompletions($conversationHistory, 'gpt-4o-mini', 'laravel-test');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
