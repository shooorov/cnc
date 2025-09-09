<?php

namespace App\Models;

use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes, StatusTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'vat_rate' => 'float',
        'service_cost' => 'float',
        'delivery_cost' => 'float',
        'barista_target' => 'float',
        'sale_target' => 'float',
        'chef_target' => 'float',
    ];

    public $statuses = [
        'pending' => 'Pending',
        'active' => 'Active',
        'archived' => 'Archived',
    ];

    public function accesses()
    {
        return $this->hasMany(BranchAccess::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
