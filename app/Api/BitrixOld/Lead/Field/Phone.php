<?php

namespace App\Api\BitrixOld\Lead\Field;

final class Phone extends Field
{
    public function __construct(
        string $phone,
    )
    {
        parent::__construct('PHONE', [
            "n0" => [
                "VALUE" => $phone,
                "VALUE_TYPE" => "WORK",
            ],
        ]);
    }
}
