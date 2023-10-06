<?php

namespace App\Api\BitrixOld\Lead;

use App\Api\BitrixOld\Lead\Field\Field;


class Fields 
{
    /** @var Field[] $fields */
    private array $fields;

    public function __construct(
        Field ...$fields
    )
    {
        $this->fields = $fields;
    }

    public function addFields(Field ...$fields): self
    {
        array_push($this->fields, ...$fields);
        return $this;
    }

    public function toArray(): array
    {
        $array = array();

        foreach ($this->fields as $field)
        {
            $array[$field->key] = $field->value;
        }

        return $array;
    }
}