<?php

namespace App\Actions\Completions;

use App\Models\Completion;
use Illuminate\Support\Facades\Auth;

/**
 * Create Completion
 */
class CreateCompletion
{
    public function __invoke($request)
    {
        Completion::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'model' => $request->input('model'),
            'instructions' => $request->input('instructions'),
            'user_id' => Auth::id(),
        ]);
    }
}
