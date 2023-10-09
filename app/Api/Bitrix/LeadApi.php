<?php

namespace App\Api\Bitrix;

use App\Api\BitrixOld;

class LeadApi
{
    public function __construct(
        private readonly BitrixOld $bitrix
    )
    {
    }

    public function create(BitrixOld\Lead\Fields $fields)
    {
        return $this->bitrix->createLead($fields->toArray());
    }

    public function update(int $id, BitrixOld\Lead\Fields $fields)
    {
        return $this->bitrix->updateLead($id, $fields->toArray());
    }

    public function get(int $id)
    {
        return $this->bitrix->getLeads($id);
    }
}