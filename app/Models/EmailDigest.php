<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailDigest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'branch_id',
        'is_checked',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];
}
