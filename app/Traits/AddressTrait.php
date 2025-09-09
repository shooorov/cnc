<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

trait AddressTrait
{
    use SoftDeletes;

    /**
     * Get the Model's address.
     */
    public function latest_address()
    {
        return $this->morphOne(Address::class, 'addressable')->latestOfMany();
    }

    /**
     * Get all of the Model's addresses.
     */
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    // /**
    //  * Get the Trait image.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Casts\Attribute
    //  */
    // public function address(): Attribute
    // {
    //     return Attribute::get(fn () => $this->latest_address?->address ?? '');
    // }
}
