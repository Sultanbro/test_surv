<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Payment;

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
            'payment_type'  => 'required|in:yookassa'
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


        return new StatusPaymentDTO(
            $paymentId,
            $paymentType
        );
    }
}
