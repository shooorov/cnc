<?php

namespace App\Models;

use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheItemInventory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemInventoryItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'direction',
        'item_id',
        'quantity',
        'rate',
        'total',
        'item_inventory_id',
        'product_inventory_product_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avg_rate',
        'item_name',
        'item_unit',
        'required_quantity_unit',
        'required_rate_total',
        'inventory_date_format',
    ];

    public function item_inventory()
    {
        return $this->belongsTo(ItemInventory::class);
    }

    public function inventory_product()
    {
        return $this->belongsTo(ProductInventoryProduct::class);
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
     * Get the item Name.
     */
    public function itemName(): Attribute
    {
        $item = CacheItem::find($this->item_id);

        return Attribute::get(fn () => $item?->name);
    }

    /**
     * Get the item Unit.
     */
    public function itemUnit(): Attribute
    {
        $item = CacheItem::find($this->item_id);

        return Attribute::get(fn () => $item?->unit);
    }

    /**
     * Get the item Unit.
     */
    public function avgRate(): Attribute
    {
        $item = CacheItem::find($this->item_id);

        return Attribute::get(fn () => floatval($item?->avg_rate));
    }

    /**
     * Get the datetime format.
     */
    public function requiredQuantityUnit(): Attribute
    {
        $quantity_format = floatval($this->quantity);
        $item = CacheItem::find($this->item_id);

        return Attribute::get(fn () => $quantity_format . ' ' . $item?->unit);
    }

    /**
     * Get the datetime format.
     */
    public function requiredRateTotal(): Attribute
    {
        $rate_format = floatval($this->rate);
        $total_format = floatval($this->total);

        return Attribute::get(fn () => " * $rate_format = $total_format tk");
    }

    /**
     * Get the datetime format.
     */
    public function inventoryDateFormat(): Attribute
    {
        $item_inventory = CacheItemInventory::find($this->item_inventory_id);

        return Attribute::get(fn () => $item_inventory->datetime_format);
    }
}
