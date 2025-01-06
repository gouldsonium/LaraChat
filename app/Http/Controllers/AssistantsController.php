<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ChatGptService;
use App\Models\Assistant;

class AssistantsController extends Controller
{
    protected function validateAssistant(Request $request): array
    {
        return $request->validate([
            'model' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:512',
            'instructions' => 'required|string|max:2000',
            'tools' => 'nullable|array',
        ]);
    }

    protected function formatTools(array $tools = null): array
    {
        return $tools ? array_map(fn($tool) => ['type' => $tool], $tools) : [];
    }

    protected function handleErrorResponse($response, string $defaultMessage)
    {
        $errorDetails = $response->json()['error']['message'] ?? $defaultMessage;
        session()->flash('flash.banner', $defaultMessage);
        session()->flash('flash.bannerStyle', 'danger');
        return back()->withErrors(['details' => $errorDetails])->withInput();
    }

    public function show()
    {
        return Inertia::render('Assistants/Show', [
            'assistants' => Assistant::all()
        ]);
    }

    public function create(Request $request, ChatGptService $chatGptService)
    {
        $validatedData = $this->validateAssistant($request);
        $tools = $this->formatTools($validatedData['tools']);

        $response = $chatGptService->createAssistant((object)array_merge($validatedData, ['tools' => $tools]));

        if ($response->successful()) {
            Assistant::create([
                'assistant_id' => $response->json()['id'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'model' => $validatedData['model'],
                'user_id' => Auth::id(),
            ]);

            session()->flash('flash.banner', 'Assistant created successfully!');
            session()->flash('flash.bannerStyle', 'success');
            return redirect()->back();
        }

        return $this->handleErrorResponse($response, 'Failed to create assistant. Please try again.');
    }

    public function manage($id, ChatGptService $chatGptService)
    {
        $assistant = Assistant::findOrFail($id);

        $response = $chatGptService->getAssistant($assistant->assistant_id);

        if ($response->successful()) {
            $jsonResponse = $response->json();

            return Inertia::render('Assistants/Manage', [
                'assistant' => array_merge($assistant->toArray(), [
                    'instructions' => $jsonResponse['instructions'],
                    'tools' => array_column($jsonResponse['tools'], 'type'),
                ]),
            ]);
        }

        return $this->handleErrorResponse($response, 'Failed to fetch assistant details.');
    }

    public function update(Request $request, $id, ChatGptService $chatGptService)
    {
        $validatedData = $this->validateAssistant($request);
        $tools = $this->formatTools($validatedData['tools']);
        $assistant = Assistant::findOrFail($id);

        $response = $chatGptService->updateAssistant($assistant->assistant_id, (object)array_merge($validatedData, ['tools' => $tools]));

        if ($response->successful()) {
            $assistant->update([
                'model' => $validatedData['model'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
            ]);

            $assistant->refresh();

            session()->flash('flash.banner', 'Assistant updated successfully!');
            session()->flash('flash.bannerStyle', 'success');

            return redirect()->back();
        }

        return $this->handleErrorResponse($response, 'Failed to update assistant. Please try again.');
    }

    public function delete($id, ChatGptService $chatGptService)
    {
        $assistant = Assistant::findOrFail($id);
        $response = $chatGptService->deleteAssistant($assistant->assistant_id);

        if ($response->successful()) {
            $assistant->delete();

            session()->flash('flash.banner', 'Assistant deleted successfully!');
            session()->flash('flash.bannerStyle', 'success');

            return redirect()->route('assistants.show');
        }

        return $this->handleErrorResponse($response, 'Failed to delte assistant. Please try again.');

    }
}
