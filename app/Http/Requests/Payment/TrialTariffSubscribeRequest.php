<?php
declare(strict_types=1);

namespace App\Http\Requests\Payment;

use App\DTO\Payment\CreateInvoiceDTO;
use App\DTO\Payment\CreateTrialPaymentSubscriptionDto;
use App\Enums\Payments\CurrencyEnum;
use App\Rules\CanUseTrialTariff;
use App\Rules\CheckCanUseTrialTariff;
use App\Rules\TariffExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class TrialTariffSubscribeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tenant_id' => ['nullable', 'string', new CanUseTrialTariff()],
        ];
    }

    /**
     * @return CreateTrialPaymentSubscriptionDto
     */
    public function toDto(): CreateTrialPaymentSubscriptionDto
    {
        $validated = $this->validated();

        $tenant = Arr::get($validated, 'tenant_id', tenant('id'));

        return new CreateTrialPaymentSubscriptionDto(
            $tenant
        );
    }
}
