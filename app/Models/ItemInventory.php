<?php

namespace App\Models;

use App\Models\Scopes\UseBranchScope;
use App\Traits\DocumentTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemInventory extends Model
{
    use DocumentTrait;
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_time',
        // 'date_formatted',
        'datetime_format',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UseBranchScope);
    }

    public function items()
    {
        return $this->hasMany(ItemInventoryItem::class);
    }

    public function product_inventory()
    {
        return $this->belongsTo(ProductInventory::class);
    }

    /**
     * Get the datetime format.
     */
    public function dateFormatted(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('d/m/Y h:i A'));
    }

    /**
     * Get the datetime format.
     */
    public function dateTime(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('Y-m-d H:i'));
    }

    /**
     * Get the datetime format.
     */
    public function datetimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('d/m/Y h:i A'));
    }
}
