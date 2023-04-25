<?php

namespace App\Http\Requests\Mailing;

use App\DTO\BaseDTO;
use App\DTO\Mailing\CreateMailingDTO;
use App\Http\Requests\BaseFormRequest;
use App\Rules\Mailing\ValidateByType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateMailingRequest extends BaseFormRequest
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
            'title'             => 'required|min:3|max:244',
            'recipients'        => ['required', 'array', new ValidateByType],
            'recipients.*.id'   => 'required|integer',

            /**
             * Передаваемые типы User => 1, ProfileGroup => 2, Position => 3
             */
            'recipients.*.type' => 'required|integer',
            'frequency' => 'required|string|in:daily,weekly,monthly'

        ];
    }

    /**
     * @return BaseDTO<CreateMailingDTO>
     */
    public function toDto(): BaseDTO
    {
        $validated  = $this->validated();

        $title      = Arr::get($validated, 'title');
        $recipients = Arr::get($validated, 'recipients');
        $frequency  = Arr::get($validated, 'frequency');

        return new CreateMailingDTO(
            $title,
            $recipients,
            $frequency
        );
    }
}
