<?php

namespace App\Http\Resources;

use App\Models\UserSignatureHistory;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserSignatureHistory $resource
 */
class SignatureHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user_id' => $this->resource->user_id,
            'name' => $this->resource->name,
            'phone' => $this->resource->phone,
            'address' => $this->resource->address,
            'contract_number' => $this->resource->contract_number,
            'password_number' => $this->resource->password_number,
            'created_at' => $this->resource->created_at
        ];
    }
}
