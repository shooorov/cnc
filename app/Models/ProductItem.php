<?php

namespace App\Models;

use App\Http\Resources\ProductCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'unit',
        'quantity',
        'item_id',
        'product_id',
    ];

    protected $appends = [
        'item_name',
        'in_stock',
        'avg_rate',
        'product_name',
    ];

    public static function collection($resource)
    {
        return tap(new ProductCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the item item_name.
     */
    public function itemName(): Attribute
    {
        return Attribute::get(fn () => $this->item->name);
    }

    /**
     * Get the item in_stock.
     */
    public function inStock(): Attribute
    {
        return Attribute::get(fn () => $this->item->in_stock);
    }

    /**
     * Get the item avg_rate.
     */
    public function avgRate(): Attribute
    {
        return Attribute::get(fn () => $this->item->avg_rate);
    }

    /**
     * Get the item product_name.
     */
    public function productNameold(): Attribute
    {
        return Attribute::get(fn () => $this->product->name);
    }
    public function productName(): Attribute
{
    return Attribute::get(function () {
        try {
            return $this->product->name;
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            \Log::error('Error accessing product name: ' . $e->getMessage());
            // Return a default value or handle the error appropriately
            return 'Unknown Product Name';
        }
    });
}

}
