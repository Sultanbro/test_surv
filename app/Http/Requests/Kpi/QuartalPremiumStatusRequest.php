<?php

namespace App\Http\Requests\Kpi;

use App\DTO\Kpi\QuartalPremiumIsActiveDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class QuartalPremiumStatusRequest extends FormRequest
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
            'premium_id'  => 'required|integer|exists:kpi_bonuses,id',
            'is_active' => 'required|bool'
        ];
    }

    /**
     * @return QuartalPremiumIsActiveDTO
     */
    public function toDto(): QuartalPremiumIsActiveDTO
    {
        $validated = $this->validated();

        $premiumId = Arr::get($validated, 'premium_id');
        $isActive = Arr::get($validated, 'is_active');

        return new QuartalPremiumIsActiveDTO(
            $premiumId,
            $isActive
        );
    }
}
