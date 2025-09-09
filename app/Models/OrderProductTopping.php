<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProductTopping extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'rate',
        'total',
        'item_id',
        'topping_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
    ];

    public function topping()
    {
        return $this->belongsTo(Topping::class);
    }
}
