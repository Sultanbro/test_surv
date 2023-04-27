<?php

namespace App\Rules\Mailing;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class ValidateWeek implements Rule
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
        $weekDays = [0, 1, 2, 3, 4, 5, 6];

        if ($value['frequency'] == 'weekly')
        {
            foreach ($value['days'] as $day)
            {
                if (!in_array($day, $weekDays))
                {
                    return false;
                }
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
        return 'The transmitted days do not correspond to the type of periodicity';
    }
}
