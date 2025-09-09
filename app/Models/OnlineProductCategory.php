<?php

namespace App\Models;

use App\Http\Cache\CacheOnlineProduct;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineProductCategory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'serial',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'image',
        'image_default',
        'item_count',
    ];

    public function items()
    {
        return $this->hasMany(OnlineProduct::class);
    }

    public function itemCount(): Attribute
    {
        $parent_id = $this->id;
        $items = CacheOnlineProduct::get()->filter(fn ($item) => $item->online_product_category_id == $parent_id);

        return Attribute::get(fn () => $items->count());
    }

    /**
     * Get the image image.
     */
    public function image(): Attribute
    {
        return Attribute::get(fn () => $this->image ?? $this->image_default);
    }

    /**
     * Get the image image.
     */
    public function imageDefault(): Attribute
    {
        return Attribute::get(fn () => asset('img/product-category.jpg'));
    }
}
