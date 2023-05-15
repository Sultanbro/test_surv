<?php

namespace App\Http\Requests\WorkChart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class BaseWorkChartRequest extends FormRequest
{ 
//    private static int $MAX_CHART_DAYS = 7;

    /**
     * Get the validated data from the request.
     *
     * @param  array|int|string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null) {
        $validated = parent::validated($key, $default);

//        $chartWorkdays  = (int) Arr::get($validated, 'chart_workdays');
//        $chartDayoffs  = (int) Arr::get($validated, 'chart_dayoffs');
//
//        if (!$chartWorkdays || !$chartDayoffs) {
//            return $validated;
//        }

//        if ($chartWorkdays + $chartDayoffs > self::$MAX_CHART_DAYS) {
//            throw new BadRequestHttpException('max chart days sum is '. self::$MAX_CHART_DAYS);
//        }

        return $validated;
    }
}
