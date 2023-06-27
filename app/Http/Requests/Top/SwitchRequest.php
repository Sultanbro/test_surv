<?php

namespace App\Http\Requests\Top;

use App\DTO\BaseDTO;
use App\DTO\Top\SwitchTopDTO;
use App\ProfileGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class SwitchRequest extends FormRequest
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
            'id' => 'required|exists:profile_groups',
            'switch_column' => ['required', Rule::in([ProfileGroup::SWITCH_UTILITY, ProfileGroup::SWITCH_PROCEEDS, ProfileGroup::SWITCH_RENTABILITY])],
            'switch_value' => ['required', Rule::in([ProfileGroup::SWITCH_ON, ProfileGroup::SWITCH_OFF])]
        ];
    }

    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'id');
        $switchColumn = Arr::get($validated, 'switch_column');
        $switchValue = Arr::get($validated, 'switch_value');

        return new SwitchTopDTO($id, $switchColumn, $switchValue);
    }
}
