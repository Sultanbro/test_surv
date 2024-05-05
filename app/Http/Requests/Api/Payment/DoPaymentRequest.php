<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

use App\DTO\Api\NewTariffPaymentDTO;
use App\Enums\Payments\CurrencyEnum;
use App\Rules\TariffExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DoPaymentRequest extends FormRequest
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
            'currency' => 'required|in:kzt,rub,usd',
            'tariff_id' => ['required', 'integer', new TariffExist],
            'extra_users_limit' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return NewTariffPaymentDTO
     */
    public function toDto(): NewTariffPaymentDTO
    {
        $validated = $this->validated();

        $currency = Arr::get($validated, 'currency');
        $tariffId = Arr::get($validated, 'tariff_id');
        $extraUsersLimit = (int)Arr::get($validated, 'extra_users_limit');
        $provider = CurrencyEnum::provider($currency);

        return new NewTariffPaymentDTO(
            $currency,
            $tariffId,
            $extraUsersLimit,
            $provider
        );
    }
}
