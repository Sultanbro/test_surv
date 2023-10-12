<?php

namespace App\Http\Requests\Referral;

use App\Rules\PhoneRule;
use App\Service\Referral\Core\ReferralRequestDto;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2']
            , 'phone' => ['required', new PhoneRule]
        ];
    }

    public function toDto(): ReferralRequestDto
    {
        $data = $this->validated();
        return new ReferralRequestDto(
              $data['name']
            , $data['phone']
        );
    }
}
