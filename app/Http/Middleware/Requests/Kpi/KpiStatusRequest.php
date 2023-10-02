<?php

namespace App\Http\Requests\Kpi;

use App\DTO\Kpi\KpiIsActiveDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class KpiStatusRequest extends FormRequest
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
            'id'     => 'required|integer|exists:kpis,id',
            'is_active' => 'required|bool'
        ];
    }

    /**
     * @return KpiIsActiveDTO
     */
    public function toDto(): KpiIsActiveDTO
    {
        $validated = $this->validated();

        $id = Arr::get($validated, 'id');
        $isActive = Arr::get($validated, 'is_active');

        return new KpiIsActiveDTO(
            $id,
            $isActive
        );
    }
}
