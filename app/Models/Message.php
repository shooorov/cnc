<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'contact',
        'content',
        'is_sent',
        'customer_id',
    ];

    protected $casts = [
        'response' => 'json',
    ];
}
