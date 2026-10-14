<?php

namespace App\Support\Response;

class JsonApiResponse implements Response
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Получаем json() данные
     */
    public function getData()
    {
        return $this->data['result'];
    }

    public function getStatus(): bool
    {
        return $this->data->status() == 200;
    }

    public function first()
    {
        return $this->getData()[0];
    }
}