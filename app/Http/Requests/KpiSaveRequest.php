<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KpiSaveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'kpiable' => ['required', 'array', 'min:1'],
            'kpiable.*.kpiable_id' => 'integer|required',
            'kpiable.*.kpiable_type' => 'required',
            'completed_80' => 'required',
            'completed_100' => 'required',
            'lower_limit' => 'required',
            'upper_limit' => 'required',
            'colors' => 'array|nullable',
            'items.*.name' => 'required',
            'items.*.plan' => 'required|numeric',
            'items.*.share' => 'required|numeric',
            'items.*.activity_id' => 'required|integer'
        ];
    }
}
