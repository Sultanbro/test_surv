<?php

namespace App\Http\Requests;

use App\DTO\Kpi\QuarterPremium\QuarterPremiumUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class QuartalPremiumUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:quartal_premiums,id',
            'activity_id'       => 'integer',
            'targetable_type'    => [
                'required',
                Rule::in([
                    'App\User',
                    'App\ProfileGroup',
                    'App\Position'
                ]),
            ],
            'title'             => 'nullable|max:100',
            'text'              => 'nullable|max:255',
            'plan'              => 'nullable',
            'from'              => 'nullable|date',
            'to'                => 'nullable|date',
            'sum'               => 'nullable'
        ];
    }

    /**
     * @return QuarterPremiumUpdateDTO
     */
    public function toDto(): QuarterPremiumUpdateDTO
    {
        $validated = $this->validated();

        $id             = Arr::get($validated, 'id');
        $activityId     = Arr::get($validated, 'activity_id');
        $targetAbleType = Arr::get($validated, 'targetable_type');
        $title  = Arr::get($validated, 'title');
        $text   = Arr::get($validated, 'text');
        $plan   = Arr::get($validated, 'plan');
        $from   = Arr::get($validated, 'from');
        $to     = Arr::get($validated, 'to');
        $sum    = Arr::get($validated, 'sum');

        return new QuarterPremiumUpdateDTO(
            $id,
            $activityId,
            $targetAbleType,
            $title,
            $text,
            $plan,
            $from,
            $to,
            $sum
        );
    }
}
