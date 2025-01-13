<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ChatGptService;
use App\Models\Assistant;
use App\Http\Requests\ValidateAssistantRequest;

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

    public function create(ValidateAssistantRequest $request, ChatGptService $chatGptService)
    {
        // Retrieve validated data from the request
        $validatedData = $request->validated();

        // Format tools using the validated data
        $tools = $this->formatTools($validatedData['tools']);

        // Merge validated data with formatted tools and send to the service
        $response = $chatGptService->createAssistant((object) array_merge($validatedData, ['tools' => $tools]));

        if ($response->successful()) {
            // Use validated data for creating the Assistant record
            Assistant::create([
                'assistant_id' => $response->json()['id'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'model' => $validatedData['model'],
                'user_id' => Auth::id(),
            ]);

            // Set success flash message
            session()->flash('flash.banner', 'Assistant created successfully!');
            session()->flash('flash.bannerStyle', 'success');

            // Redirect back
            return redirect()->back();
        }

        // Handle error response
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

    public function update(ValidateAssistantRequest $request, $id, ChatGptService $chatGptService)
    {
        // Retrieve validated data from the request
        $validatedData = $request->validated();

        // Format tools using the validated data
        $tools = $this->formatTools($validatedData['tools']);

        $assistant = Assistant::findOrFail($id);

        // Update the assistant via api
        $response = $chatGptService->updateAssistant(
            $assistant->assistant_id,
            (object) array_merge($validatedData, ['tools' => $tools])
        );

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

        // Handle error response
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

        return $this->handleErrorResponse($response, 'Failed to delete assistant. Please try again.');
    }
}
