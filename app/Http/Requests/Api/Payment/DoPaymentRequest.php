<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

use App\DTO\Api\PaymentDTO;
use App\Enums\Payments\CurrencyEnum;
use App\Rules\CheckTariffLimit;
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
            'currency' => 'required|in:kzt,rub,dollar',
            'tariff_id' => ['required', 'integer', 'exists:tariff,id', new TariffExist],
            'extra_users_limit' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return PaymentDTO
     */
    public function toDto(): PaymentDTO
    {
        $validated = $this->validated();

        $currency = Arr::get($validated, 'currency');
        $tariffId = Arr::get($validated, 'tariff_id');
        $extraUsersLimit = Arr::get($validated, 'extra_users_limit');
        $provider = CurrencyEnum::provider($currency);

        return new PaymentDTO(
            $currency,
            $tariffId,
            $extraUsersLimit,
            $provider
        );
    }
}
