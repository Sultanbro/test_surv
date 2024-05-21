<?php

namespace App\Rules;

use App\Repositories\PromoCode\PromoCodeRepositoryInterface;
use Illuminate\Contracts\Validation\Rule;

class ValidPromoCode implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        /** @var PromoCodeRepositoryInterface $repository */
        $repository = app(PromoCodeRepositoryInterface::class);
        return $repository->exitsValidItem($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The promo code must be exists or valid.';
    }
}
