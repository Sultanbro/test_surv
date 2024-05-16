<?php
declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\Enums\Payments\CurrencyEnum;
use App\Rules\TariffExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class CreateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => 'required|in:kzt,rub,usd',
            'tariff_id' => ['required', 'integer', new TariffExist],
            'tenant_id' => ['nullable', 'string'],
            'extra_users_limit' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return CreateInvoiceDTO
     */
    public function toDto(): CreateInvoiceDTO
    {
        $validated = $this->validated();

        $currency = Arr::get($validated, 'currency');
        $tariffId = Arr::get($validated, 'tariff_id');
        $extraUsersLimit = (int)Arr::get($validated, 'extra_users_limit');
        $provider = CurrencyEnum::provider($currency);
        $tenant = Arr::get($validated, 'tenant_id', tenant('id'));

        return new CreateInvoiceDTO(
            $currency,
            $tariffId,
            $tenant,
            $extraUsersLimit,
            $provider
        );
    }
}
