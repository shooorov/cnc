<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  ItemInventory  $item_inventory
     * @return Response
     */
    public function destroy(Document $document)
    {
        if ($document) {
            if (Storage::exists($document->path)) {
                unlink(Storage::path($document->path));
            }
            $document->delete();

            return back()->with('success', __('Document removed successfully!'));
        }

        return back()->with('fail', __('Document removing failed'));
    }
}
