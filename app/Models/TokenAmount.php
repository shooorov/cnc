<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenAmount extends Model
{
    use HasFactory;

    protected $casts = [
        'amount' => 'float',
    ];

    protected $fillable = [
        'product_category_id',
        'branch_id',
        'amount',
    ];
}
