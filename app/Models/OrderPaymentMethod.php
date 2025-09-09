<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
