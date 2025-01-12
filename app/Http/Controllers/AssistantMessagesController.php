<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Assistant;
use App\Models\AssistantThread;
use App\Models\User;
use App\Services\ChatGptService;

class AssistantMessagesController extends Controller
{
    protected function transformMessagesToConversationHistory(array $messages): array
    {
        $conversationHistory = [];

        // Reverse the messages data array
        $messagesData = array_reverse($messages['data']);

        foreach ($messagesData as $message) {
            // Extract the role
            $role = $message['role'];

            // Extract the content, handling the content array
            $content = collect($message['content'])
                ->pluck('text.value')
                ->implode(' '); // Join multiple content parts into a single string if necessary

            // Add the transformed message to the conversation history
            $conversationHistory[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        return $conversationHistory;
    }

    public function chat(int $id, ChatGptService $chatGptService)
    {
        /** @var User $user */
        $user = Auth::user();
        $assistant = Assistant::findOrFail($id);
        $thread = $user->getThreadByAssistantId($id);

        if(!$thread){
            $createThreadResponse = $chatGptService->createThread();
            if ($createThreadResponse->successful()) {
                $threadId = $createThreadResponse->json('id');

                $thread = AssistantThread::create([
                    'thread_id' => $threadId,
                    'assistant_id' => $id,
                    'user_id' => Auth::id()
                ]);
            } else {
                // Log the error for debugging if response is not successful
                Log::error('Failed to fetch response from OpenAI', [
                    'response' => $createThreadResponse->body(),
                ]);
            }
        }

        $response = $chatGptService->getThreadMessages($thread->thread_id);
        $conversationHistory = $this->transformMessagesToConversationHistory($response->json());

        return Inertia::render('Assistants/Chat', [
            'assistant' => $assistant,
            'conversationHistory' => $conversationHistory,
            'threadId' => $thread->thread_id
        ]);
    }

    public function send(Request $request, ChatGptService $chatGptService)
    {
        try {
            $request->validate([
                'message' => 'required|string',
                'thread_id' => 'required|string',
                'assistant_id' => 'required|string'
            ]);

            // Step 1: Create the message in the thread
            $createMessageResponse = $chatGptService->createThreadMessage(
                $request->input('thread_id'),
                $request->input('message')
            );

            if ($createMessageResponse->successful()) {
                // Step 2: Create the run
                $createRunResponse = $chatGptService->createRun(
                    $request->input('thread_id'),
                    $request->input('assistant_id'),
                );

                if ($createRunResponse->successful()) {
                    $runId = $createRunResponse->json()['id'];  // Assuming the response includes the run ID

                    // Step 3: Poll for the run status
                    $status = $chatGptService->pollRunStatus($request->input('thread_id'), $runId);

                    // After the run is created, reload the page
                    if ($status === 'completed') {
                        return Inertia::location(url()->previous()); // Redirect without full reload
                    };
                }
            };

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

    public function deleteThread(int $id, ChatGptService $chatGptService)
    {
        /** @var User $user */
        $user = Auth::user();
        $thread = $user->getThreadByAssistantId($id);

        $response = $chatGptService->deleteThread($thread->thread_id);

        if($response->successful()){
            $thread->delete();
            return Inertia::location(url()->previous()); // Redirect without full reload
        }
    }
}
