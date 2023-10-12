<?php

namespace App\Service\Referral\Core;

use App\Classes\Helpers\Phone;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadTemplate
{
    const SEGMENT_ID = 3548;

    public function __construct(
        private readonly ReferralInterface    $referral
        , private readonly ReferralRequestDto $request
    )
    {
    }

    public function get(string $key = null): array|string|null
    {
        $user = $this->referral->referrer->user;
        $hash = Hash::make(uniqid() . mt_rand());
        $countries = Countries::toArray();
        $data = [
            "TITLE" => "Удаленный " . $this->request->name,
            "NAME" => $this->request->name,
            'UF_CRM_1498210379' => self::SEGMENT_ID, // сегмент
            "UF_CRM_1635442762" => $countries[Phone::getCountry($this->request->phone)], //страна
            "ASSIGNED_BY_ID" => 23900, // Валерия Сидоренко
            "UF_CRM_1635487718862" => 'https://wa.me/+' . Phone::normalize($this->request->phone), // Ват сап линк
            'UF_CRM_1624530685082' => config('services.intellect.time_link') . $hash, // Ссылка для офисных кандидатов
            'UF_CRM_1624530730434' => config('services.intellect.contract_link') . $hash, // Ссылка для удаленных кандидатов
            "PHONE" => [["VALUE" => $this->request->phone, "VALUE_TYPE" => "WORK"]],
            "UF_CRM_1658397129" => '', // город
            "UF_CRM_1658163204" => $this->referral->url(),
            "UF_CRM_1693466068" => $user->name . ' ' . $user->last_name
        ];
        if ($key) {
            $adapted = [];
            foreach ($data as $arrayKey => $item) {
                $adapted[Str::lower($arrayKey)] = $item;
            }
            return $adapted[$key] ?? null;
        }

        return $data;
    }
}