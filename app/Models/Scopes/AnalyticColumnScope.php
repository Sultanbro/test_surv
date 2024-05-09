<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AnalyticColumnScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        // we deleted this so this should be responsible to prevent usage of plan column in whole project
        // but also there is another case when using query builder instead of eloquent
        $builder->whereNot('name', 'plan');
    }
}
