<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
    ];

    /**
     * Get the parent documentable model (Document).
     */
    public function documentable()
    {
        return $this->morphTo();
    }

    protected $appends = [
        'full_path',
    ];

    /**
     * Get the Trait image.
     */
    public function fullPath(): Attribute
    {
        return Attribute::get(fn () => Storage::url($this->path) ?? null);
    }
}
