<?php

namespace App\Models\Scopes;

use App\UseBranch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UseBranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $branch_id = UseBranch::id();

        $builder->when($branch_id, function ($query, $branch_id) {
            $query->where('branch_id', $branch_id);
        });
    }
}
