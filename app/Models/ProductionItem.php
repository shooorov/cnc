<?php

namespace App\Models;

use App\Http\Cache\CacheProduct;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ProductionItem extends Model
{
    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Accept',
        'complete' => 'Complete',
        // 'hand-over' => 'Hand Over',
    ];

    protected $fillable = [
        'status',
        'quantity',
        'product_id',
        'production_id',
        'order_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product',
        'product_name',
    ];

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    /**
     * Get the Product name.
     */
    public function product(): Attribute
    {
        return Attribute::get(fn () => CacheProduct::find($this->product_id));
    }

    /**
     * Get the Product name.
     */
    public function productName(): Attribute
    {
        return Attribute::get(fn () => $this->product?->name);
    }
}
