<?php

namespace App\Actions\Completions;

use App\Models\Completion;

/**
 * Update Completion
 */
class UpdateCompletion
{
    public function __invoke(int $id, $request)
    {
        $completion = Completion::findOrFail($id);
        $completion->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'model' => $request->input('model'),
            'instructions' => $request->input('instructions'),
        ]);
    }
}
