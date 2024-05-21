<?php

namespace App\Rules;

use App\Repositories\Tariffs\TariffPaymentRepository;
use Illuminate\Contracts\Validation\Rule;

class TariffExist implements Rule
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
        return !(new TariffPaymentRepository)->tariffForOwnerAlreadyExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'У вас уже имеется тариф';
    }
}
