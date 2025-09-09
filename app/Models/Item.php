<?php

namespace App\Models;

use App\Http\Cache\CacheItemCategory;
use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use ImageTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'item_category_id',
        'unit',
        'avg_rate',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'category_name',
        // 'in_stock',
    ];

    public $units = [
        'kg' => 'Kilogram',
        'l' => 'Liter',
        'pcs' => 'Pieces',
    ];

    public function item_category()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function inventories()
    {
        return $this->hasMany(ItemInventoryItem::class);
    }

    /**
     * Get the item category name.
     */
    public function categoryName(): Attribute
    {
        $category_id = $this->item_category_id;

        return Attribute::get(fn () => CacheItemCategory::find($category_id)?->name);
    }

    /**
     * Get the In Stock Quantity.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // public function inStock(): Attribute
    // {
    //     $item_quantity = $this->inventories->where('direction', 'in')->sum('quantity') - $this->inventories->where('direction', 'out')->sum('quantity');
    //     return Attribute::get(fn () => $item_quantity ?? 0);
    // }

    // public function updateAvgRate()
    // {
    //     $this->avg_rate = ItemInventoryItem::where('item_id', $this->id)->where('direction', 'in')->avg('rate');
    //     $this->save();
    // }
}
