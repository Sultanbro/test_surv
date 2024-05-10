<?php
declare(strict_types=1);

namespace App\Rules;

use App\Models\Tariff\Tariff;
use Illuminate\Contracts\Validation\Rule;

class CheckTariffLimit implements Rule
{
    /**
     * @var int
     */
    private int $tariffId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        int $tariffId
    )
    {
        $this->tariffId = $tariffId;
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
        $tariff = Tariff::find( $this->tariffId);

        if ($tariff->users_limit < $value)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Количество пользователей превышается для этого тарифного плана, выберите другой тариф!';
    }
}
