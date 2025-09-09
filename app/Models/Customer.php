<?php

namespace App\Models;

use App\Http\Cache\CacheCustomerOrder;
use App\Http\Cache\CacheSetting;
use App\Http\Cache\CacheUser;
use App\Traits\AddressTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Model
{
    use AddressTrait, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'address',
        'user_id',
    ];

    protected $appends = [
        'order_count',
        'user_name',
        'delivery_address',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customer_tokens()
    {
        return $this->hasMany(CustomerToken::class);
    }

    public function orderDeliveries()
    {
        return $this->hasMany(OrderDelivery::class);
    }

    public function tokenUpdate()
    {
        $collect = CustomerToken::sum('token');
        $used = Order::whereCustomerId($this->id)->sum('token');
        $token = ($collect - $used);
        $this->token = $token;
        $this->save();
    }

    /**
     * Get the item category name.
     */
    public function orderCount(): Attribute
    {
        $customer_id = $this->id;

        return Attribute::get(fn () => CacheCustomerOrder::find($customer_id, 'customer_id')?->order_quantity);
    }

    public function userName(): Attribute
    {
        $user_id = $this->creator_id;

        return Attribute::get(fn () => $user_id ? CacheUser::find($user_id, 'id')?->name : '');
    }

    public function balance(): Attribute
    {
        $settings = CacheSetting::get()->pluck('value', 'name')->toArray();

        return Attribute::get(fn () => $this->token * ($settings['token_value'] ?? 0));
    }

    public function deliveryAddress(): Attribute
    {
        $customer_id = $this->id;

        return Attribute::get(fn () => OrderDelivery::where('customer_id', $customer_id)->whereNotNull('address')->latest()->first()?->address ?? '');
    }
}
