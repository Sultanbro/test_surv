<?php

namespace App\Api\Bitrix;

use App\Api\BitrixOld\Lead\Fields;

interface LeadApiInterface
{
    public function create(Fields $fields): mixed;

    public function update(int $id, Fields $fields): mixed;

    public function get(int $id): mixed;
}