<?php

namespace App\Rules;

use App\Models\CentralUser;
use Illuminate\Contracts\Validation\Rule;

class CheckCentralUsers implements Rule
{
    private string $column;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($column)
    {
        $this->column = $column;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !CentralUser::query()->where($this->column, $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->column == 'email') {
            return 'Другой аккаунт уже привязан к данному адресу почты';
        }
        return 'Другой аккаунт уже привязан к данному номеру телефона';
    }
}
