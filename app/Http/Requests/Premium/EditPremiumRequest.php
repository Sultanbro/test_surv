<?php

namespace App\Http\Requests\Premium;

use App\DTO\Premium\EditPremiumDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class EditPremiumRequest extends FormRequest
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
            'type'      => ['required', 'string'],
            'user_id'   => ['required', 'numeric', 'exists:users,id'],
            'amount'    => ['required', 'numeric', 'min:0'],
            'comment'   => ['required', 'string', 'min:3'],
            'date'      => ['required', 'string']
        ];
    }

    /**
     * @return EditPremiumDTO
     */
    public function toDto(): EditPremiumDTO
    {
        $validated = $this->validated();

        $type   = Arr::get($validated, 'type');
        $userId = Arr::get($validated, 'user_id');
        $amount = Arr::get($validated, 'amount');
        $comment = Arr::get($validated, 'comment');
        $date   = Arr::get($validated, 'date');

        return new EditPremiumDTO(
            $type,
            $userId,
            $amount,
            $comment,
            $date
        );
    }
}
