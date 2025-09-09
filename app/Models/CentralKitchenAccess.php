<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentralKitchenAccess extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_checked',
        'central_kitchen_id',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];
}
