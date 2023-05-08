<?php

namespace App\Rules\Mailing;

use App\Enums\Mailing\MailingEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidateDaily implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if ($value['frequency'] == MailingEnum::DAILY)
        {
            if (!empty($value['days']))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'If type of frequency is daily array should be empty';
    }
}
