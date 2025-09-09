<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'order_id',
        'order_product_id',
        'employee_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
