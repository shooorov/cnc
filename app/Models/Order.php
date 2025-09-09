<?php

namespace App\Models;

use App\Http\Cache\CacheCustomer;
use App\Http\Cache\CacheOrderDelivery;
use App\Http\Cache\CacheUser;
use App\Models\Scopes\UseBranchScope;
use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, StatusTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'number',
        'type',
        'sub_total',
        'discount_type',
        'discount_amount',
        'vat_rate',
        'vat_amount',
        'delivery_cost',
        'total',
        'cash',
        'change',
        'token',
        'method_id',
        'waiter_id',
        'customer_id',
        'creator_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:d/m/Y',
        'history' => 'array',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'datetime_raw',
        'datetime_format',
        'rider_id',
        'customer_name',
        'customer_mobile',
        'customer_balance',
        'creator_name',
        'waiter_name',
        'payment_method_name',
        'current_delivery_status',
        'description',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'accept' => 'Accept',
        'complete' => 'Complete',
        'delivered'     => 'Delivered',
    ];

    public $discount_types = [
        ['id' => 'percent', 'name' => 'Percent'],
        ['id' => 'flat',    'name' => 'Flat'],
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function methods()
    {
        return $this->hasMany(OrderPaymentMethod::class);
    }

    public function orderHistories()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function stuff()
    {
        return $this->belongsTo(Stuff::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function customer_token()
    {
        return $this->hasOne(CustomerToken::class);
    }

    public function order_delivery()
    {
        return $this->hasOne(OrderDelivery::class);
    }

    public function product_inventory()
    {
        return $this->hasOne(ProductInventory::class);
    }

    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    /**
     * Get the waiter_name.
     */
    public function waiterName(): Attribute
    {
        $id = $this->waiter_id;
        $ob = CacheUser::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->name);

        // return Attribute::get(fn () => $this->employee?->name);
    }

    /**
     * Get the rider_id.
     */
    public function riderId(): Attribute
    {
        $id = $this->id;
        $ob = CacheOrderDelivery::get()->first(fn ($item) => $id == $item->order_id);

        return Attribute::get(fn () => $ob?->rider?->rider_id);

        // return Attribute::get(fn () => $this->order_delivery?->rider_id);
    }

    /**
     * Get the rider_name.
     */
    public function riderName(): Attribute
    {
        $id = $this->id;
        $ob = CacheOrderDelivery::get()->first(fn ($item) => $id == $item->order_id);

        return Attribute::get(fn () => $ob?->rider?->name);

        // return Attribute::get(fn () => $this->order_delivery?->rider?->name);
    }

    /**
     * Get the current_delivery_status.
     */
    public function currentDeliveryStatus(): Attribute
    {
        $current_status = 'It\'s on the way!';
        $status = $this->status;

        if ($status == 'pending') {
            $current_status = 'Our manager will confirm your order!';
        } elseif ($status == 'accept') {
            $current_status = 'Your order confirmed!';
        } elseif ($status == 'complete') {
            if ($this->order_delivery?->status == 'collect') {
                $current_status = 'It\'s on the way!';
            } elseif ($this->order_delivery?->status == 'delivered') {
                $current_status = 'Your order has been delivered!';
            } else {
                $current_status = 'Rider not picked your order yet!';
            }
        } else {
            $current_status = 'We are preparing your order!';
        }

        return Attribute::get(fn () => $current_status);
    }

    /**
     * Get the delivery_status.
     */
    public function deliveryStatus(): Attribute
    {
        $id = $this->id;
        $ob = CacheOrderDelivery::get()->first(fn ($item) => $id == $item->order_id);

        return Attribute::get(fn () => $ob?->status);

        // return Attribute::get(fn () => $this->order_delivery?->status);
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
     * Get the customer_balance.
     */
    public function customerBalance(): Attribute
    {
        $id = $this->customer_id;
        $ob = CacheCustomer::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->token);
    }

    /**
     * Get the creator_name.
     */
    public function creatorName(): Attribute
    {
        $id = $this->creator_id;
        $ob = CacheUser::get()->first(fn ($item) => $id == $item->id);

        return Attribute::get(fn () => $ob?->name);
    }

    /**
     * Get the description.
     */
    public function description(): Attribute
    {
        $products = $this->products;
        $qty = $products->count();
        $units = $products->sum('quantity');

        return Attribute::get(fn () => $qty . ' ' . str('item')->plural($qty) . ' ' . $units . ' ' . str('unit')->plural($units));
    }

    /**
     * Get the payment_method_name.
     */
    public function paymentMethodName(): Attribute
    {
        $name = PaymentMethod::whereIn('id', $this->methods->pluck('payment_method_id'))->pluck('name')->join(', ');

        return Attribute::get(fn () => $name);
    }

    /**
     * Get the datetime_format.
     */
    public function datetimeFormat(): Attribute
    {
        return Attribute::get(fn () => $this->date?->format('d/m/Y h:i A'));
    }

    /**
     * Get the datetime_raw.
     */
    public function datetimeRaw(): Attribute
    {
        return Attribute::get(fn () => $this->date?->format('Y-m-d\TH:i'));
    }
}
