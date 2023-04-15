<?php

namespace App\Http\Requests\Kpi\Statistics;

use App\DTO\BaseDTO;
use App\DTO\Kpi\Statistic\UserGroupDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UserGroupsRequest extends BaseFormRequest
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
            'user_id' => 'required|integer|exists:users,id',
            'year'    => 'integer',
            'month'   => 'integer'
        ];
    }

    /**
     * @return BaseDTO
     */
    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $userId = Arr::get($validated, 'user_id');
        $year = Arr::get($validated, 'year');
        $month = Arr::get($validated, 'month');

        return new UserGroupDTO(
            $userId,
            $year,
            $month
        );
    }
}
