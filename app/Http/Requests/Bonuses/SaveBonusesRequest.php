<?php

namespace App\Http\Requests\Bonuses;

use App\DTO\Bonuses\SaveBonusesDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class SaveBonusesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'bonuses'               => 'required|array',
            'bonuses.*.title'       => 'required|string',
            'bonuses.*.sum'         => 'required|integer',
            'bonuses.*.group_id'    => 'required|integer',
            'bonuses.*.activity_id' => 'required|integer',
            'bonuses.*.unit'        => 'required|string|in:all,one,first,percent',
            'bonuses.*.quantity'    => [
                'required_unless:bonuses.*.unit,percent',
                'integer',
                'min:1',
                'nullable'
            ],
            'bonuses.*.daypart'     => 'required|integer|in:0,1,2',
            'bonuses.*.text'        => 'nullable',
            'bonuses.*.from'        => 'nullable',
            'bonuses.*.to'          => 'nullable',
            'bonuses.*.targetable_id'   => 'required|integer',
            'bonuses.*.targetable_type' => 'required|integer|in:1,2,3',
        ];
    }

    /**
     * @return SaveBonusesDTO
     */
    public function toDto(): SaveBonusesDTO
    {
        $validated = $this->validated();

        $bonuses = Arr::get($validated, 'bonuses');

        return new SaveBonusesDTO($bonuses);
    }
}
