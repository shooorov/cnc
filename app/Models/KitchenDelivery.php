<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KitchenDelivery extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'name',
        'branch_name',
        'datetime_format',
    ];

    public function items()
    {
        return $this->hasMany(KitchenDeliveryItem::class, 'kitchen_delivery_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function central_kitchen()
    {
        return $this->belongsTo(CentralKitchen::class);
    }

    /**
     * Get the datetime format.
     */
    public function datetimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date->format('d/m/Y h:i A'));
    }

    /**
     * Get the branch_name.
     */
    public function branchName(): Attribute
    {
        return Attribute::get(fn () => $this->branch->name);
    }

    /**
     * Get the name.
     */
    public function name(): Attribute
    {
        return Attribute::get(fn () => "$this->branch_name - $this->datetime_format");
    }
}
