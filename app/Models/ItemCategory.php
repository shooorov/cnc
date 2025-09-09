<?php

namespace App\Models;

use App\Http\Cache\CacheItem;
use App\Http\Cache\CacheItemCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemCategory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'item_category_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'category_name',
        'item_count',
    ];

    public function item_category()
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function itemCount(): Attribute
    {
        $parent_id = $this->id;
        $items = CacheItem::get()->filter(fn ($item) => $item->item_category_id == $parent_id);

        return Attribute::get(fn () => $items->count());
    }

    /**
     * Get the item category name.
     */
    public function categoryName(): Attribute
    {
        $parent_id = $this->item_category_id;

        return Attribute::get(fn () => CacheItemCategory::find($parent_id)?->name);
    }
}
