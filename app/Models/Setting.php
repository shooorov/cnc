<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'title',
        'value',
    ];

    protected $casts = [
        'hidden' => 'boolean',
    ];

    public static $hidden_types = [
        'hidden',
        'rename',
    ];
}
