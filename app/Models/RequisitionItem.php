<?php

namespace App\Models;

use App\Http\Cache\CacheItem;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
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
        'item_id',
        'requisition_id',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'item_name',
        'item_unit',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the Image Default.
     */
    public function itemName(): Attribute
    {
        return Attribute::get(fn () => CacheItem::find($this->item_id)->name);
    }

    /**
     * Get the Image Default.
     */
    public function itemUnit(): Attribute
    {
        return Attribute::get(fn () => CacheItem::find($this->item_id)->unit);
    }
}
