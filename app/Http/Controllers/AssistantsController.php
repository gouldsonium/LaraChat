<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\ChatGptService;


class AssistantsController extends Controller
{
    public function show()
    {
        return Inertia::render('Assistants/Show');
    }

    public function create(Request $request, ChatGptService $chatGptService)
    {
        $validatedData = $request->validate([
            'model' => 'required|string',
            'name' => 'required|string|max:255',
            'instructions' => 'required|string|max:2000',
            'tools' => 'nullable|array', // Allow tools to be optional
        ]);

        // Convert tools array to the required format
        $tools = isset($validatedData['tools']) ? array_map(function($tool) {
            return ['type' => $tool];
        }, $validatedData['tools']) : [];

        $data = [
            'model' => $validatedData['model'],
            'name' => $validatedData['name'],
            'instructions' => $validatedData['instructions'],
            'tools' => $tools, // Use the transformed tools array
        ];

        $response = $chatGptService->createAssistant((object)$data); // Convert array to object

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Assistant created successfully!');
        } else {
            return back()->withErrors([
                'error' => 'Failed to create assistant. Please try again.',
                'details' => $response->json(),
            ])->withInput();
        }
    }

}
