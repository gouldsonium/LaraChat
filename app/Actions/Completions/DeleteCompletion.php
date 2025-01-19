<?php

namespace App\Actions\Completions;

use App\Models\Completion;

/**
 * Delete Completion
 */
class DeleteCompletion
{
    public function __invoke(int $id)
    {
        $completion = Completion::findOrFail($id);
        $completion->delete();
    }
}
