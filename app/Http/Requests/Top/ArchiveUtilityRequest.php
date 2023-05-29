<?php

namespace App\Http\Requests\Top;

use App\DTO\BaseDTO;
use App\DTO\Top\ArchiveUtilityDTO;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ArchiveUtilityRequest extends BaseFormRequest
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
            'group_id'   => 'required|integer|exists:profile_groups,id',
            'is_archive' => 'required|boolean'
        ];
    }

    /**
     * @return BaseDTO<ArchiveUtilityDTO>
     */
    public function toDto(): BaseDTO
    {
        $validated = $this->validated();

        $isArchive = Arr::get($validated, 'is_archive');
        $groupId = Arr::get($validated, 'group_id');

        return new ArchiveUtilityDTO($groupId, $isArchive);
    }
}
