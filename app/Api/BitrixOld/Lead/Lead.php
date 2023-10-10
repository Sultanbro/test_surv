<?php

namespace App\Api\BitrixOld\Lead;

use App\Api\BitrixOld;
use App\Api\BitrixOld\Lead\Fields;
use Exception;

class Lead
{
    private ?bool $isNeedCallback;

    public function __construct(
        protected Fields $fields = new Fields(),
        private ?BitrixOld $bitrix,
    )
    {
        $this->bitrix = $this->bitrix ?? new BitrixOld();
    }

    final public function setNeedCallback(bool $isNeedCallback): self
    {
        $this->isNeedCallback = $isNeedCallback;
        return $this;
    }

    /**
     * @throws Exception
     */
    final public function publish(): mixed
    {
        $result = $this->bitrix->createLead(
            $this->fields->toArray(),
            $this->isNeedCallback,
        );

        if (is_array($result) && array_key_exists('error', $result)) {
            throw new Exception('publish lead error');
        }

        return $result;
    } 
}