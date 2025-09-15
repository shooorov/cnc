<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitchenDelivery extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function items()
    {
        return $this->hasMany(KitchenDeliveryItem::class, 'kitchen_delivery_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function central_kitchen()
    {
        return $this->belongsTo(CentralKitchen::class);
    }
}
