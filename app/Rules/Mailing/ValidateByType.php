<?php

namespace App\Rules\Mailing;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use \Illuminate\Validation\Rule as Validation;

class ValidateByType implements Rule
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
        $recipients = $value;
        foreach ($recipients as $recipient)
        {
            if ($recipient['type'] == 1)
            {
                return DB::table('users')->where('id', $recipient['id'])->exists();
            }

            if ($recipient['type'] == 2)
            {
                return DB::table('profile_groups')->where('id', $recipient['id'])->exists();
            }

            if ($recipient['type'] == 3)
            {
                return DB::table('position')->where('id', $recipient['id'])->exists();
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
        return 'No query result for one the :attribute';
    }
}
