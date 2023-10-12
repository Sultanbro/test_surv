<?php

namespace App\Service\Referral\Core;

use App\Api\Bitrix\LeadApiInterface;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Fields;
use App\Models\Bitrix\Lead;

class ReferralLeadService implements ReferralLeadServiceInterface
{
    public function __construct(
        private readonly LeadApiInterface $leadApi
    )
    {
    }

    public function create(ReferralInterface $referral, ReferralRequestDto $request): void
    {
        $data = new LeadTemplate($referral, $request);
        $fields = $this->fields($data->get());
        $bitrixLead = $this->leadApi->create($fields);

        Lead::query()->create([
            'lead_id' => $bitrixLead['result'],
            'name' => $data->get('name'),
            'phone' => $request->phone,
            'status' => 'NEW',
            'segment' => Lead::getSegmentAlt($data->get('segment')),
            'hash' => $data->get('hash')
        ]);
    }

    private function fields(array $data): Fields
    {
        $fields = new Fields();
        foreach ($data as $key => $value) {
            $fields->addFields(new Field($key, $value));
        }
        return $fields;
    }
}