<?php

namespace App\Traits;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Eloquent;

/**
 * @method static Builder|Eloquent filter(QueryFilter $filter)
 *
 * @mixin Eloquent
 */
trait Filterable
{
    public static function scopeFilter(Builder $builder, QueryFilter $filter): Builder
    {
        return $filter->apply($builder);
    }
}
