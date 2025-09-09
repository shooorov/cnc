<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'factor',
        'from_unit_id',
        'to_unit_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'from_unit_name',
        // 'to_unit_name',
    ];

    public $timestamps = false;

    public function from_unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function to_unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
