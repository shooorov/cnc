<?php

namespace App\Models;

use App\Http\Cache\CacheProductCategory;
use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use ImageTrait, SoftDeletes;

	public function __construct()
    {
        //$this->default_status = 'active';

		$this->default_image = '/img/product.jpg';
		$this->directory = 'products';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'rate',
        'discount',
        'description',
        'product_category_id',
        //'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'vat_applicable' => 'boolean',
        'rate' => 'float',
        'production_cost' => 'float',
        'margin_percent' => 'float',
        'margin_amount' => 'float',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_platter',
        'category_name',
    ];

    public $units = [
        'g' => 'Gram',
        'ml' => 'MilliLiter',
        'pcs' => 'Pieces',
    ];
    public $statuses = [
        //'pending' => 'Pending',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function order_products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function inventories()
    {
        return $this->hasMany(ProductInventoryProduct::class);
    }

    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }

    public function platters()
    {
        return $this->hasMany(Platter::class);
    }

    public function getCostAttribute()
    {
    }

    /**
     * Get the item category name.
     */
    public function isPlatter(): Attribute
    {
        return Attribute::get(fn () => CacheProductCategory::find($this->product_category_id)?->is_platter);
    }

    /**
     * Get the item category name.
     */
    public function categoryName(): Attribute
    {
        return Attribute::get(fn () => CacheProductCategory::find($this->product_category_id)?->name);
    }
}
