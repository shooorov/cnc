<?php

namespace App\Models;

use App\Models\Scopes\UseBranchScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductRequisition extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UseBranchScope);
    }

    public function products()
    {
        return $this->hasMany(ProductRequisitionItem::class, 'product_requisition_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
