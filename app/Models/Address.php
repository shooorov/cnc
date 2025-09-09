<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
    ];

    /**
     * Get the parent documentable model (Document).
     */
    public function documentable()
    {
        return $this->morphTo();
    }
}
