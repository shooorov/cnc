<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Platter extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function item()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
}
