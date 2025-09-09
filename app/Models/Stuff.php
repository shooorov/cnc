<?php

namespace App\Models;

use App\Http\Cache\CacheBranch;
use App\Models\Scopes\UseBranchScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Stuff extends Model
{
    /**
     * The accessors to append to the Model's array form.
     *
     * @var array
     */
    protected $appends = [
        'branch_name',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new UseBranchScope);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the item branch_name.
     */
    public function branchName(): Attribute
    {
        $branch_id = $this->branch_id;

        return Attribute::get(fn () => CacheBranch::find($branch_id)?->name);
    }
}
