<?php

namespace App\Api\Bitrix;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Fields;

class LeadApi implements LeadApiInterface
{
    public function __construct(
        private readonly BitrixOld $bitrix
    )
    {
    }

    public function create(Fields $fields): mixed
    {
        return $this->bitrix->createLead($fields->toArray());
    }

    public function update(int $id, Fields $fields): mixed
    {
        return $this->bitrix->updateLead($id, $fields->toArray());
    }

    public function get(int $id): mixed
    {
        return $this->bitrix->getLeads($id);
    }
}