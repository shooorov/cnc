<?php

namespace App\Models;

use App\Http\Cache\CacheBranch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $appends = [
        'branch_name',
        'vat_rate',
        'service_cost',
        'delivery_cost',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the branch name.
     */
    public function branchName(): Attribute
    {
        return Attribute::get(fn () => CacheBranch::find($this->branch_id)?->name);
    }

    /**
     * Get the branch name.
     */
    public function vatRate(): Attribute
    {
        return Attribute::get(fn () => CacheBranch::find($this->branch_id)?->vat_rate);
    }

    /**
     * Get the branch name.
     */
    public function serviceCost(): Attribute
    {
        return Attribute::get(fn () => CacheBranch::find($this->branch_id)?->service_cost);
    }

    /**
     * Get the branch name.
     */
    public function deliveryCost(): Attribute
    {
        return Attribute::get(fn () => CacheBranch::find($this->branch_id)?->delivery_cost);
    }
}
