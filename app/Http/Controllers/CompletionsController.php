<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Completion;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ValidateCompletionRequest;

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
}
