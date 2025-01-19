<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ValidateChatRequest;
use App\Models\Assistant;
use App\Models\User;
use App\Actions\Threads\{
    CreateThread as CreateThreadAction,
    GetThreadMessages as GetThreadMessagesAction,
    DeleteThread as DeleteThreadAction,
    SendThreadMessage as SendThreadMessageAction
};

class AssistantMessagesController extends Controller
{
    public function chat(int $id, CreateThreadAction $createThreadAction, GetThreadMessagesAction $getThreadMessages)
    {
        $assistant = Assistant::findOrFail($id);

        /** @var User $user */
        $user = Auth::user();
        $thread = $user->getThreadByAssistantId($id);

        // If no thread exists, create one using the provided action
        if (!$thread) {
            $thread = $createThreadAction($id);
        }

        $conversationHistory = $getThreadMessages($thread->thread_id);

        return Inertia::render('Assistants/Chat', [
            'assistant' => $assistant,
            'conversationHistory' => $conversationHistory,
            'threadId' => $thread->thread_id
        ]);
    }

    public function send(int $assistantId, ValidateChatRequest $request, SendThreadMessageAction $sendThreadMessage)
    {
        try {
            $assistant = Assistant::findOrFail($assistantId);
            $validatedData = $request->validated();

            /** @var User $user */
            $user = Auth::user();
            $thread = $user->getThreadByAssistantId($assistantId);

            $reply = $sendThreadMessage($thread->thread_id, $assistant->assistant_id, $validatedData['message']);

            return response()->json([
                'reply' => $reply
            ]);

        } catch (\Exception $e) {
            // Catch and log any exception for debugging
            Log::error('Error in assistant send method', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function deleteThread(int $id, DeleteThreadAction $deleteThreadAction, CreateThreadAction $createThreadAction)
    {
        $deleteThreadAction($id);
        $createThreadAction($id); # Create empty thread to replace the old one
        return response()->json(['message' => 'Thread deleted'], 200);
    }
}
