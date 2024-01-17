<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuartalPremiumSaveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'targetable_id' => 'required|integer',
            'targetable_type' => [
                'required',
                Rule::in([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
            ],
            'activity_id' => 'nullable',
            'title' => 'required|max:100',
            'text' => 'required|max:255',
            'plan' => 'required',
            'cell' => 'nullable|string',
            'from' => 'required|date',
            'to' => 'required|date'
        ];
    }
}
