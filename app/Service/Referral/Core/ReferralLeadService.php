<?php

namespace App\Service\Referral\Core;

use App\Api\Bitrix\LeadApi;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Fields;

class ReferralLeadService implements ReferralLeadServiceInterface
{
    private ReferralInterface $referral;
    private array $data;

    public function __construct(
        private readonly LeadApi $leadApi
    )
    {
    }

    public function create(ReferralInterface $referral, array $request): void
    {
        $this->referral = $referral;
        $this->data = $request;
        $this->leadApi->create($this->fields());
    }

    private function fields(): Fields
    {
        $fields = new Fields();
        foreach ($this->data as $key => $value) {
            $field = new Field($key, $value);
            $fields->addFields($field);
        }
        $fields->addFields(new Field('реферальная ссылка', $this->referral->url()));
        return $fields;
    }
}