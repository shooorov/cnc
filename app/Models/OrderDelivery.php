<?php

namespace App\Models;

use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheUser;
use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    use StatusTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'address',
        'order_id',
        'customer_id',
        'rider_id',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Accept',
        'collect' => 'Collect',
        'delivered' => 'Delivered',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'customer_name',
        'customer_mobile',
        'rider_name',
        'collection_time',
        'delivery_time',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'collected_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    /**
     * Get the customer_name.
     */
    public function customerName(): Attribute
    {
        $id = $this->customer_id;
        $ob = CacheCustomer::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->name);
    }

    /**
     * Get the customer_mobile.
     */
    public function customerMobile(): Attribute
    {
        $id = $this->customer_id;
        $ob = CacheCustomer::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->mobile);
    }

    /**
     * Get the creator_name.
     */
    public function riderName(): Attribute
    {
        $id = $this->rider_id;
        $ob = CacheUser::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->name);
    }

    /**
     * Get the creator_name.
     */
    public function collectionTime(): Attribute
    {
        if (empty($this->collected_at)) {
            $time = $this->accepted_at?->diffInMinutes(now()).' minutes passed';
        } else {
            $time = $this->accepted_at?->diffInMinutes($this->collected_at).' minutes';
        }

        return Attribute::get(fn () => $time);
    }

    /**
     * Get the creator_name.
     */
    public function deliveryTime(): Attribute
    {
        if (empty($this->delivered_at)) {
            $time = $this->collected_at?->diffInMinutes(now()).' minutes passed';
        } else {
            $time = $this->collected_at?->diffInMinutes($this->delivered_at).' minutes';
        }

        return Attribute::get(fn () => $time);
    }
}
