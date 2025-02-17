<?php

namespace App\Http\Resources\Responses;

use Illuminate\Contracts\Support\Arrayable;

class JsonSuccessResponse implements Arrayable
{
    private array $data;
    private string $message;

    public function __construct(string $message, array $data = [])
    {
        $this->data = $data;
        $this->message = $message;
    }

    public function toArray(): array
    {
        return array_merge(
            [
                'message' => $this->message,
            ],
            $this->data ? ['data' => $this->data] : [],
        );
    }
}
