<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply scope if user is authenticated and has a branch
        if (auth()->check() && auth()->user()->branch_id) {
            $branchId = auth()->user()->branch_id;
            
            // Apply branch filter based on model type
            if (in_array('branch_id', $model->getFillable())) {
                $builder->where($model->getTable() . '.branch_id', $branchId);
            }
        }
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutBranchScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('withAllBranches', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('forBranch', function (Builder $builder, $branchId) {
            return $builder->withoutGlobalScope($this)->where('branch_id', $branchId);
        });
    }
}
