<?php

declare(strict_types=1);

namespace App\Support\Core;

use Illuminate\Http\Exceptions\HttpResponseException;

final class CustomException
{
    public function __construct(
        public string $message,
        public int $code,
        public array $data,
    )
    {
        $this->handle();
    }

    public function handle(): void
    {
        throw new HttpResponseException(response()->json([
            'message'   => $this->message,
            'data'      => $this->data
        ], $this->code));
    }
}
