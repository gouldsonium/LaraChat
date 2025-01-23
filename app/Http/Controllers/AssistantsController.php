<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Assistant;
use App\Http\Requests\ValidateAssistantRequest;
use App\Actions\Assistants\{
    GetAssistant as GetAssistantAction,
    CreateAssistant as CreateAssistantAction,
    UpdateAssistant as UpdateAssistantAction,
    DeleteAssistant as DeleteAssistantAction
};
use Exception;

class AssistantsController extends Controller
{
    protected function formatTools(array $tools = null): array
    {
        return $tools ? array_map(fn($tool) => ['type' => $tool], $tools) : [];
    }

    public function show()
    {
        return Inertia::render('Assistants/Show', [
            'assistants' => Assistant::all()
        ]);
    }

    public function create(ValidateAssistantRequest $request, CreateAssistantAction $createAssistantAction)
    {
        try {
            $validatedData = $request->validated();
            $createAssistantAction($validatedData);

            $this->showMessage('Assistant created successfully!');
            return redirect()->back();
        } catch (Exception $e) {
            $this->showMessage($e->getMessage(), 'danger');
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function manage($id, GetAssistantAction $getAssistantAction)
    {
        try {
            $assistant = $getAssistantAction($id);
            return Inertia::render('Assistants/Manage', [
                'assistant' => $assistant
            ]);
        } catch (Exception $e) {
            $this->showMessage($e->getMessage(), 'danger');
            return redirect()->route('assistants.show');
        }
    }

    public function update(ValidateAssistantRequest $request, int $id, UpdateAssistantAction $updateAssistantAction)
    {
        try {
            $validatedData = $request->validated();
            $updateAssistantAction($id, $validatedData);

            $this->showMessage('Assistant updated successfully!');
            return redirect()->back();
        } catch (Exception $e) {
            $this->showMessage($e->getMessage(), 'danger');
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function delete($id, DeleteAssistantAction $deleteAssistantAction)
    {
        try {
            $deleteAssistantAction($id);

            $this->showMessage('Assistant deleted successfully!');
            return redirect()->route('assistants.show');
        } catch (Exception $e) {
            $this->showMessage($e->getMessage(), 'danger');
            return redirect()->back();
        }
    }
}
