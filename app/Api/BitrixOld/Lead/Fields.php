<?php

namespace App\Api\BitrixOld\Lead;


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

        /** @var Field $field */
        foreach ($this->fields as $field)
        {
            $array[$field->key] = $field->value;
        }

        return $array;
    }
}