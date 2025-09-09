<?php

namespace App\Models;

use App\Http\Cache\CacheBranch;
use App\Http\Cache\CacheProduct;
use App\Http\Cache\CacheTokenAmount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'token',
        'is_platter',
        'product_category_id',
    ];

    protected $casts = [
        'is_platter' => 'boolean',
    ];

    protected $types = [
        'chef' => 'Chef',
        'barista' => 'Barista',
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
        'token_amount',
    ];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function itemCount(): Attribute
    {
        $parent_id = $this->id;
        $items = CacheProduct::get()->filter(fn ($item) => $item->product_category_id == $parent_id);

        return Attribute::get(fn () => $items->count());
    }

    /**
     * Get the role name.
     */
    public function tokenAmount(): Attribute
    {
        $token = [];
        $product_category_id = $this->id;

        foreach (CacheBranch::get() as $branch) {
            $amount = CacheTokenAmount::get()->first(fn ($t) => $t->product_category_id == $product_category_id && $t->branch_id == $branch->id)?->amount;
            $token[$branch->id] = $amount > 0 ? $amount : null;
        }

        return Attribute::get(fn () => $token);
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
