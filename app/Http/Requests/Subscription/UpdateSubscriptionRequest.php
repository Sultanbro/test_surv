<?php
declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\DTO\Payment\NewSubscriptionDTO;
use App\Facade\Payment\Gateway;
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
            'currency' => ['nullable', 'string'],
        ];
    }

    /**
     * @return NewSubscriptionDTO
     */
    public function toDto(): NewSubscriptionDTO
    {
        $validated = $this->validated();
        /** @var TariffSubscription $subscription */
        $subscription = $this->subscription;
        $extraUsersLimit = (int)Arr::get($validated, 'extra_users_limit');
        $tenant = Arr::get($validated, 'tenant_id', tenant('id'));

        return new NewSubscriptionDTO(
            $subscription->getCurrency(),
            $subscription->tariff_id,
            $tenant,
            $extraUsersLimit,
            Gateway::provider($validated['currency'] ?? $subscription->getCurrency())->name()
        );
    }
}
