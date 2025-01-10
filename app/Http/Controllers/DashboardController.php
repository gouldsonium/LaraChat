<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Completion;
use App\Models\Assistant;

class DashboardController extends Controller
{
    public function index()
    {
        $completions = Completion::all();
        $assistants = Assistant::all();
        $models = collect(config('chat-gpt.models'))
        ->map(fn($details, $name) => [
            'name' => $name,
            'input' => $details['input'],
            'output' => $details['output'],
        ])
        ->values()
        ->toArray();

        return Inertia::render('Dashboard', [
            'completions' => $completions,
            'assistants' => $assistants,
            'models' => $models
        ]);
    }
}
