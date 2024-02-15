<?php

namespace App\Http\Resources\Signature;

use App\Models\SmsCode;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property SmsCode $resource
 */
class SmsCodeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'code' => $this->resource->code
        ];
    }
}
