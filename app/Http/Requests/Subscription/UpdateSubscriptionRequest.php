<?php
declare(strict_types=1);

namespace App\Http\Requests\Subscription;

use App\DTO\Payment\CreateInvoiceDTO;
use App\DTO\Payment\UpdateInvoiceDTO;
use App\Enums\Payments\CurrencyEnum;
use App\Rules\TariffExist;
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
     * @return UpdateInvoiceDTO
     */
    public function toDto(): UpdateInvoiceDTO
    {
        $validated = $this->validated();

        $extraUsersLimit = (int)Arr::get($validated, 'extra_users_limit');
        $tenant = Arr::get($validated, 'tenant_id', tenant('id'));

        return new UpdateInvoiceDTO(
            $tenant,
            $extraUsersLimit,
        );
    }
}
