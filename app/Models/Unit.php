<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $appends = [
        'conversions',
    ];

    /**
     * Get the Unit conversions.
     */
    public function conversions(): Attribute
    {
        return Attribute::get(fn () => $this->hasMany(UnitConversion::class, 'from_unit_id')->get());
    }
}
