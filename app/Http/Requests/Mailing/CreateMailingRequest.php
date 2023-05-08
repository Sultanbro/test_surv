<?php

namespace App\Http\Requests\Mailing;

use App\DTO\BaseDTO;
use App\DTO\Mailing\CreateMailingDTO;
use App\Enums\Mailing\MailingEnum;
use App\Http\Requests\BaseFormRequest;
use App\Rules\Mailing\ValidateByType;
use App\Rules\Mailing\ValidateDaily;
use App\Rules\Mailing\ValidateWeek;
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
            'name'              => 'required|string',
            'title'             => 'required|min:3|max:244',
            'recipients'        => ['required', 'array', new ValidateByType],
            'recipients.*.id'   => 'required|integer',

            /**
             * Передаваемые типы User => 1, ProfileGroup => 2, Position => 3
             */
            'recipients.*.type' => 'required|integer',

            /**
             * Есть разные виды рассылки Bitrix24, u-marketing utc.
             */
            'type_of_mailing'   => 'required|array',

            'date'              => ['required', 'array', new ValidateWeek, new ValidateDaily],
            'date.days'         => [in_array($this->date['frequency'], [MailingEnum::WEEKLY, MailingEnum::MONTHLY]) ? 'required' : '', 'array'],
            'date.frequency'    => 'required|string|in:weekly,monthly,daily',
            'time'              => 'required|string'

        ];
    }

    /**
     * @return BaseDTO<CreateMailingDTO>
     */
    public function toDto(): BaseDTO
    {
        $validated  = $this->validated();

        $name       = Arr::get($validated, 'name');
        $title      = Arr::get($validated, 'title');
        $recipients = Arr::get($validated, 'recipients');
        $date       = Arr::get($validated, 'date');
        $time       = Arr::get($validated, 'time');
        $typeOfMailing  = Arr::get($validated, 'type_of_mailing');

        return new CreateMailingDTO(
            $name,
            $title,
            $recipients,
            $date,
            $time,
            $typeOfMailing
        );
    }
}
