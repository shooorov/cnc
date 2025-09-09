<?php

namespace App\Traits;

use App\Models\Signature;
use Illuminate\Support\Facades\Storage;

trait SignatureTrait
{
    /**
     * Get the Model's signature.
     */
    public function latest_signature()
    {
        return $this->morphOne(Signature::class, 'signaturable')->latestOfMany();
    }

    /**
     * Get all of the Model's signatures.
     */
    public function signatures()
    {
        return $this->morphMany(Signature::class, 'signaturable');
    }

    // /**
    //  * Get the signature.
    //  *
    //  * @return string
    //  */
    // public function getSignatureAttribute()
    // {
    //     $path = null;
    //     if ($this->latest_signature && Storage::exists('thumbnails/' . $this->latest_signature->path)) {
    //         $path = Storage::url('thumbnails/' . $this->latest_signature->path);
    //     }
    //     return $path ?? asset('img/signature.jpg');
    // }
}
