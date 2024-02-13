<?php

namespace App\Service\Sms;

class ReceiverDto
{
    public function __construct(
        public string  $phone,
        public ?string $name = null,
        public ?string $surname = null
    )
    {
    }

    public function toArray(): array
    {
        $data = [
            'phone' => $this->phone,
            'name' => $this->name,
            'surname' => $this->surname
        ];
        return array_filter($data);
    }
}