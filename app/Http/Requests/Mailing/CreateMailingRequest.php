<?php

namespace App\Http\Requests\Mailing;

use App\DTO\BaseDTO;
use App\DTO\Mailing\CreateMailingDTO;
use App\Enums\Mailing\MailingEnum;
use App\Http\Requests\BaseFormRequest;
use App\Rules\Mailing\ValidateByType;
use App\Rules\Mailing\ValidateDaily;
use App\Rules\Mailing\ValidateWeek;
use App\Rules\ValidationFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

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
            'title'             => 'required|min:3|max:1000',
            'recipients'        => [
                'array',
                new ValidateByType,
                new ValidationFrequency($this->date['frequency']),
                !in_array($this->date['frequency'], [
                    MailingEnum::TRIGGER_MANAGER_ASSESSMENT, MailingEnum::TRIGGER_FIRED, MailingEnum::TRIGGER_FIRED_WITHOUT_POLL, MailingEnum::TRIGGER_COACH_ASSESSMENT
                ]) ? 'required' : '',
            ],
            'recipients.*.id'   => 'required|integer',

            /**
             * Передаваемые типы User => 1, ProfileGroup => 2, Position => 3
             */
            'recipients.*.type' => ['required', 'integer', new ValidationFrequency($this->date['frequency'])],

            /**
             * Есть разные виды рассылки Bitrix24, u-marketing utc.
             */
            'type_of_mailing'   => ['required', 'array', Rule::in(['in-app', 'whatsapp', 'bitrix'])],

            'date'              => ['required', 'array', new ValidateWeek, new ValidateDaily],
            'date.days'         => [in_array($this->date['frequency'], [MailingEnum::WEEKLY, MailingEnum::MONTHLY]) ? 'required' : '', 'array'],
            'date.frequency'    => ['required', 'string', Rule::in(MailingEnum::FREQUENCIES)],
            'is_template'       => 'boolean',
            'count'             => 'integer'
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
        $recipients = Arr::get($validated, 'recipients') ?? [];
        $date       = Arr::get($validated, 'date');
        $typeOfMailing  = Arr::get($validated, 'type_of_mailing');
        $isTemplate = Arr::get($validated, 'is_template') ?? 0;
        $count      = Arr::get($validated, 'count') ?? 1;

        return new CreateMailingDTO(
            $name,
            $title,
            $recipients,
            $date,
            $typeOfMailing,
            $isTemplate,
            $count
        );
    }
}
