<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

trait CanArchive
{
    /**
     * Get the model class associated with this controller.
     * Must be implemented by the using controller.
     */
    abstract protected function getModelClass(): string;

    /**
     * Toggle the archive status of a record.
     */
    public function toggleArchive(Request $request, $id): RedirectResponse
    {
        $modelClass = $this->getModelClass();
        $record = $modelClass::findOrFail($id);
        
        $record->is_archived = !$record->is_archived;
        $record->save();
        
        $status = $record->is_archived ? 'archived' : 'unarchived';
        
        return redirect()->back()
            ->with('success', "Item was successfully {$status}.");
    }
}
