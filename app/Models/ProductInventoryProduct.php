<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductInventoryProduct extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'direction',
        'product_id',
        'product_inventory_id',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Accept',
        'done' => 'Done',
        'delivered' => 'Delivered',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(ItemInventoryItem::class);
    }
}
