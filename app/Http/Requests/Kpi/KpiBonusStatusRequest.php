<?php

namespace App\Http\Requests\Kpi;

use App\DTO\Kpi\KpiBonusIsActiveDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class KpiBonusStatusRequest extends FormRequest
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
            'bonus_id'  => 'required|integer|exists:kpi_bonuses,id',
            'is_active' => 'required|bool'
        ];
    }

    /**
     * @return KpiBonusIsActiveDTO
     */
    public function toDto(): KpiBonusIsActiveDTO
    {
        $validated = $this->validated();

        $bonusId = Arr::get($validated, 'bonus_id');
        $isActive = Arr::get($validated, 'is_active');

        return new KpiBonusIsActiveDTO(
            $bonusId,
            $isActive
        );
    }
}
