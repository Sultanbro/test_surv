<?php

namespace App\Service\Referral;

use App\Api\Bitrix\LeadApiInterface;
use App\Api\BitrixOld\Lead\Field\Field;
use App\Api\BitrixOld\Lead\Fields;
use App\Models\Bitrix\Lead;
use App\Service\Referral\Core\LeadServiceInterface;
use App\Service\Referral\Core\LeadTemplate;
use App\Service\Referral\Core\ReferrerInterface;
use App\Service\Referral\Core\RequestDto;
use Throwable;

class LeadService implements LeadServiceInterface
{
    public function __construct(
        private readonly LeadApiInterface $leadApi
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function create(ReferrerInterface $referrer, RequestDto $request): void
    {
        $data = new LeadTemplate($referrer, $request);
        $fields = $this->fields($data->get());

        $bitrixLead = $this->leadApi->create($fields);
//        throw_if(!array_key_exists('result', $bitrixLead), 'cant create lead in bitrix');
        Lead::query()->create([
            'lead_id' => $bitrixLead['result'],
            'name' => $data->get('name'),
            'phone' => $request->phone,
            'status' => 'NEW',
            'segment' => LeadTemplate::SEGMENT_ID,
            'hash' => $data->get('hash'),
            'referrer_id' => $referrer->id,
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