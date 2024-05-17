<?php
declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\DTO\Payment\NewInvoiceDTO;
use App\Models\Tariff\TariffSubscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'string'],
            'extra_users_limit' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return NewInvoiceDTO
     */
    public function toDto(): NewInvoiceDTO
    {
        $validated = $this->validated();
        $extraUsersLimit = (int)Arr::get($validated, 'extra_users_limit');
        $tenant = Arr::get($validated, 'tenant_id', tenant('id'));
        $existingSubscription = TariffSubscription::getValidTariffPayment($tenant);

        return new NewInvoiceDTO(
            $existingSubscription->getCurrency(),
            $existingSubscription->tariff_id,
            $tenant,
            $extraUsersLimit,
            $existingSubscription->payment_provider,
        );
    }
}
