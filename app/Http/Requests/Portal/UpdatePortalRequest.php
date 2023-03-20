<?php

namespace App\Http\Requests\Portal;

use App\DTO\Portal\UpdatePortalDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdatePortalRequest extends FormRequest
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
            'mainPageVideo' => 'url',
            'mainPageVideoShowDaysAmount' => 'integer',
        ];
    }

    /**
     * @return UpdatePortalDTO
     */
    public function toDto(string $tenantId): UpdatePortalDTO
    {
        $validated = $this->validated();

        $mainPageVideo = Arr::get($validated, 'mainPageVideo');
        $mainPageVideoShowDaysAmount = (int) Arr::get($validated, 'mainPageVideoShowDaysAmount');

        return new UpdatePortalDTO(
            $tenantId,
            $mainPageVideo,
            $mainPageVideoShowDaysAmount,
        );
    }
}
