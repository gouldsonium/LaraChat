<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Completion;
use App\Http\Requests\ValidateCompletionRequest;
use App\Http\Requests\ValidateChatRequest;
use App\Actions\Completions\{
    CreateCompletion as CreateCompletionAction,
    GetCompletionChat as GetCompletionChatAction,
    UpdateCompletion as UpdateCompletionAction,
    DeleteCompletion as DeleteCompletionAction,
    SendCompletion as SendCompletionAction
};

class CompletionsController extends Controller
{
    public function show()
    {
        return Inertia::render('Completions/Show', [
            'completions' => Completion::all()
        ]);
    }

    public function create(ValidateCompletionRequest $request, CreateCompletionAction $createCompletionAction)
    {
        $createCompletionAction($request);
        $this->showMessage('Completion created successfully!');
        return redirect()->back();
    }

    public function manage($id, GetCompletionChatAction $getCompletionChatAction)
    {
        $data = $getCompletionChatAction($id);
        return Inertia::render('Completions/Manage', [
            'completion' => $data['completion']
        ]);
    }

    public function update(ValidateCompletionRequest $request, int $id, UpdateCompletionAction $updateCompletionAction)
    {
        $updateCompletionAction($id, $request);
        $this->showMessage('Completion updated successfully!');
        return redirect()->back();
    }

    public function delete($id, DeleteCompletionAction $deleteCompletionAction)
    {
        $deleteCompletionAction($id);
        $this->showMessage('Completion deleted successfully!');
        return redirect()->route('completions.show');
    }

    public function chat(int $id, GetCompletionChatAction $getCompletionChatAction)
    {
        $data = $getCompletionChatAction($id);
        return Inertia::render('Completions/Chat', [
            'completion' => $data['completion'],
            'conversationHistory' => $data['conversationHistory']
        ]);
    }

    public function send(ValidateChatRequest $request, int $id, SendCompletionAction $sendCompletionAction)
    {
        try {
            $validatedData = $request->validated();
            $reply = $sendCompletionAction($id, $validatedData['message']);

            return response()->json([
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            $this->showMessage($e->getMessage(), 'danger');
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function clearConversation(Request $request, int $id)
    {
        $request->session()->forget('conversation_history_' . $id);
        return response()->json(['message' => 'Conversation cleared successfully'], 200);
    }
}
