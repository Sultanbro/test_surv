<?php

namespace App\Http\Requests\Referral;

use App\Rules\PhoneRule;
use App\Service\Referral\Core\RequestDto;
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

    public function toDto(): RequestDto
    {
        $data = $this->validated();
        return new RequestDto(
              $data['name']
            , $data['phone']
        );
    }
}
