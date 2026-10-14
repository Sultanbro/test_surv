<?php

namespace App\Rules;

use App\Enums\Mailing\MailingEnum;
use Illuminate\Contracts\Validation\Rule;

class ValidationFrequency implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private string $frequency
    )
    {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $isTrigger = in_array($this->frequency, [MailingEnum::TRIGGER_MANAGER_ASSESSMENT, MailingEnum::TRIGGER_FIRED, MailingEnum::TRIGGER_COACH_ASSESSMENT]);

        if (!$isTrigger)
        {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Триггерное уведомление не должен иметь получателей.';
    }
}
