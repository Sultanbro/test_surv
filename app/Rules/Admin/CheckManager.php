<?php

namespace App\Rules\Admin;

use App\Models\CentralUser;
use App\Models\Role;
use App\User;
use Illuminate\Contracts\Validation\Rule;


class CheckManager implements Rule
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
        $role = Role::query()->withWhereHas('users',
            fn($user) => $user->where('id', $value))
        ->where('name', 'Manager для работы с клиентами JobTron')->first();

        return $role || $value == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Manager is not defined';
    }
}
