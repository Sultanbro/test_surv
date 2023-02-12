<?php
declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\DTO\Api\StatusPaymentDTO;
use App\Rules\CheckTariffLimit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StatusPaymentRequest extends FormRequest
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
            'payment_id'    => 'required|string',
            'payment_type'  => 'required|in:yookassa',
            'tariff_id'     => 'required|exists:tariff,id',
            'extra_users_limit' => ['required', 'integer', new CheckTariffLimit($this->tariff_id)],
            'auto_payment'  => 'required|bool'
        ];
    }

    /**
     * @return StatusPaymentDTO
     */
    public function toDto(): StatusPaymentDTO
    {
        $validated = $this->validated();

        $paymentId   = Arr::get($validated, 'payment_id');
        $paymentType = Arr::get($validated, 'payment_type');
        $tariffId    = Arr::get($validated, 'tariff_id');
        $extraUserLimits = Arr::get($validated, 'extra_users_limit');
        $autoPayment = Arr::get($validated, 'auto_payment');

        return new StatusPaymentDTO(
            $paymentId,
            $paymentType,
            $tariffId,
            $extraUserLimits,
            $autoPayment
        );
    }
}
