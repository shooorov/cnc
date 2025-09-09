<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineProduct extends Model
{
    use ImageTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'online_product_category_id',
    ];

    public function online_product_category()
    {
        return $this->belongsTo(OnlineProductCategory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
