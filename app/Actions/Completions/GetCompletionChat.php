<?php

namespace App\Actions\Completions;

use App\Models\Completion;

/**
 * Get Completion and its chat history
 */
class GetCompletionChat
{
    public function __invoke(int $id)
    {
        $completion = Completion::findOrFail($id);
        $conversationHistory = session('conversation_history_' . $id, []);

        // Add full system message to the conversation if it's the first message
        if (empty($conversationHistory)) {
            $systemMessage = $completion->instructions;
            $conversationHistory[] = ['role' => 'system', 'content' => $systemMessage];
            session(['conversation_history_' . $id => $conversationHistory]);
        }

        return ['completion' => $completion, 'conversationHistory' => $conversationHistory];
    }

}
