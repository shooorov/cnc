<?php

namespace App\Models;

use App\Http\Cache\CacheProduct;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ProductRequisitionItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'avg_rate',
        'total',
        'product_id',
        'product_requisition_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product_name',
        'product_unit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the Image Default.
     */
    public function productName(): Attribute
    {
        return Attribute::get(fn () => CacheProduct::find($this->product_id)->name);
    }

    /**
     * Get the Image Default.
     */
    public function productUnit(): Attribute
    {
        return Attribute::get(fn () => CacheProduct::find($this->product_id)->unit);
    }
}
