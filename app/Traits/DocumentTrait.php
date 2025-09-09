<?php

namespace App\Traits;

use App\Models\Document;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

trait DocumentTrait
{
    /**
     * Get the Model's latest document.
     */
    public function latest_document()
    {
        return $this->morphOne(Document::class, 'documentable')->latestOfMany();
    }

    /**
     * Get all of the Model's documents.
     */
    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    // /**
    //  * Get the Trait image.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Casts\Attribute
    //  */
    // public function document(): Attribute
    // {
    //     $path = null;
    //     if ($this->latest_document && Storage::exists($this->latest_document->path)) {
    //         $path = Storage::url($this->latest_document->path);
    //     }
    //     return Attribute::get(fn () => $path);
    // }
}
