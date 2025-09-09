<?php

namespace App\Traits;

use App\Models\Remark;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

trait RemarkTrait
{
    // use SoftDeletes;

    /**
     * Get the Model's remark.
     */
    public function latest_remark()
    {
        return $this->morphOne(Remark::class, 'remarkable')->latestOfMany();
    }

    /**
     * Get all of the Model's remarks.
     */
    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    // /**
    //  * Get the Trait image.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Casts\Attribute
    //  */
    // public function remark(): Attribute
    // {
    //     return Attribute::get(fn () => $this->latest_remark?->remark ?? '');
    // }
}
