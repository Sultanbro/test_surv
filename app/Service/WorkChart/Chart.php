<?php

namespace App\Service\WorkChart;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;

interface Chart
{
    public function chartProcess(Builder $builder): bool;
}