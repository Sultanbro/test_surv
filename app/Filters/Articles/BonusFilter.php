<?php

namespace App\Filters\Articles;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BonusFilter extends QueryFilter
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->params = $this->request->get('filters') ?? [];
    }

    public function dataFrom(array $params): Builder
    {
        $date = \Carbon\Carbon::createFromDate(
            $params['year'] ?? now()->year, $params['month'] ?? now()->month
        )->startOfMonth();

        return $this->builder
            ->where(function ($query) use ($date) {
                $query->whereNull('deleted_at');
                $query->orWhere('deleted_at', '>', $date->format('Y-m-d'));
            });
    }

    public function groupId(int $groupId): Builder
    {
        return $this->builder
            ->where('targetable_type', 'App\\ProfileGroup')
            ->where('targetable_id', $groupId);
    }
    public function query(string|null $search)
    {

    }

    //  Eto poka ne nado
//    public function createdAt(array $params): Builder
//    {
//        return $this->builder->where();
//    }
}
