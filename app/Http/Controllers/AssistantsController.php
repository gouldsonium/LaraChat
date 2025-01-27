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
use Illuminate\Http\Request;
use App\Services\ChatGptService;
use Illuminate\Support\Facades\Storage;

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
            $showFiles = in_array('file_search', $assistant['tools']);

            return Inertia::render('Assistants/Manage', [
                'assistant' => $assistant,
                'showFiles' => $showFiles,
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

    /**
     * Handle file upload for assistants.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request, ChatGptService $chatGptService)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:txt,json,csv,pdf', // Adjust size and type as needed
        ]);

        $filePath = null;

        try {
            $file = $request->file('file');
            $filePath = $file->store('uploads'); // Store the file and get the path
            $fullPath = Storage::disk('local')->path($filePath);

            $response = $chatGptService->uploadFile($fullPath, 'assistants');

            return response()->json([
                'message' => 'File uploaded successfully.',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload file.',
                'error' => $e->getMessage(),
            ], 500);
        } finally {
            // Delete the file if it exists
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }
    }

}
