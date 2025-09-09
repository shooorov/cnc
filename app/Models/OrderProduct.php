<?php

namespace App\Models;

use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes, StatusTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch',
        'status',
        'rate',
        'quantity',
        'total',
        'product_id',
        'order_id',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Accept',
        'complete' => 'Complete',
        // 'hand-over' => 'Hand Over',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'product_name',
        'vat_applicable',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function toppings()
    {
        return $this->hasMany(OrderProductTopping::class, 'product_id');
    }

    /**
     * Get the product name.
     */
    public function productName(): Attribute
    {
        return Attribute::get(fn () => $this->product?->name);
    }

    /**
     * Get the product name.
     */
    public function vatApplicable(): Attribute
    {
        return Attribute::get(fn () => $this->product?->vat_applicable);
    }
}
