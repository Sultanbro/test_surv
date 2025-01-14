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

        Lead::query()->create([
            'lead_id' => $bitrixLead['result'],
            'name' => $request->name,
            'phone' => $request->phone,
            'status' => 'NEW',
            'segment' => 26,
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