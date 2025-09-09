<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topping extends Model
{
    use ImageTrait, SoftDeletes;

    protected $appends = [
        // 'image',
        'image_default',
    ];

    /**
     * Get the Image Default.
     */
    public function imageDefault(): Attribute
    {
        return Attribute::get(fn () => asset('img/topping.jpg'));
    }
}
