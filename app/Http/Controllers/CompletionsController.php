<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Completion;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidateCompletionRequest;
use Illuminate\Http\Request;
use App\Services\ChatGptService;
use Illuminate\Support\Facades\Log;

class CompletionsController extends Controller
{
    public function show()
    {
        $completions = Completion::all();
        return Inertia::render('Completions/Show', [
            'completions' => $completions
        ]);
    }

    public function create(ValidateCompletionRequest $request)
    {
        Completion::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'model' => $request->input('model'),
            'instructions' => $request->input('instructions'),
            'user_id' => Auth::id(),
        ]);

        session()->flash('flash.banner', 'Completion created successfully!');
        session()->flash('flash.bannerStyle', 'success');
        return redirect()->back();
    }

    public function manage($id)
    {
        $completion = Completion::findOrFail($id);
        return Inertia::render('Completions/Manage', [
            'completion' => $completion
        ]);
    }

    public function update(ValidateCompletionRequest $request, $id)
    {
        $completion = Completion::findOrFail($id);
        $completion->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'model' => $request->input('model'),
            'instructions' => $request->input('instructions'),
        ]);

        $completion->refresh();

        session()->flash('flash.banner', 'Completion updated successfully!');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->back();

        // Handle error response
        // return $this->handleErrorResponse($response, 'Failed to update assistant. Please try again.');
    }

    public function delete($id)
    {
        $completion = Completion::findOrFail($id);
        $completion->delete();

        session()->flash('flash.banner', 'Completion deleted successfully!');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('completions.show');

        // return $this->handleErrorResponse($response, 'Failed to delte assistant. Please try again.');
    }

    public function chat(int $id)
    {
        $completion = Completion::findOrFail($id);

        // Retrieve the previous conversation from the session
        $conversationHistory = session('conversation_history_' . $id, []);

        // Add full system message to the conversation if it's the first message
        if (empty($conversationHistory)) {
            $systemMessage = $completion->instructions;
            $conversationHistory[] = ['role' => 'system', 'content' => $systemMessage];
            session(['conversation_history_' . $id => $conversationHistory]);
        }
        return Inertia::render('Completions/Chat', [
            'completion' => $completion,
            'conversationHistory' => $conversationHistory
        ]);
    }

    public function send(Request $request, int $id, ChatGptService $chatGptService)
    {
        try {
            $request->validate([
                'message' => 'required|string',
            ]);

            $completion = Completion::findOrFail($id);
            $conversationHistory = session('conversation_history_' . $id, []);

            $conversationHistory[] = [
                'role' => 'user',
                'content' => $request->input('message'),
            ];

            $response = $chatGptService->createCompletions($conversationHistory, $completion->model);

            if ($response->successful()) {
                // Get the system's reply
                $reply = $response->json('choices')[0]['message']['content'];

                $conversationHistory[] = [
                    'role' => 'system',
                    'content' => $reply,
                ];

                // Save the conversation history in the session
                session(['conversation_history_' . $id => $conversationHistory]);

                return Inertia::location(url()->previous()); // Redirect without full reload
            } else {
                // Log the error for debugging if response is not successful
                Log::error('Failed to fetch response from OpenAI', [
                    'response' => $response->body(),
                ]);
            }

        } catch (\Exception $e) {
            // Catch and log any exception for debugging
            Log::error('Error in send method', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function clearConversation(Request $request, int $id)
    {
        $request->session()->forget('conversation_history_' . $id);
        return redirect()->back();
    }
}
