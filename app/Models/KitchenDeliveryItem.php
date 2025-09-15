<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KitchenDeliveryItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requisition_quantity',
        'delivery_quantity',
        'rate',
        'avg_rate',
        'total',
        'product_id',
        'kitchen_delivery_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'item_name',
        'item_unit',
    ];

    /**
     * Relationship with Product.
     */
    public function item()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Relationship with KitchenDelivery.
     */
    public function kitchenDelivery()
    {
        return $this->belongsTo(KitchenDelivery::class, 'kitchen_delivery_id');
    }

    /**
     * Get the product name from cache or relationship.
     */
    public function itemName(): Attribute
    {
        return Attribute::get(fn() => $this->item?->name ?? 'N/A');
    }

    /**
     * Get the product unit from cache or relationship.
     */
    public function itemUnit(): Attribute
    {
        return Attribute::get(fn() => $this->item?->unit ?? 'N/A');
    }
}
