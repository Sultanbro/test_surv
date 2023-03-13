<?php

namespace App\Http\Resources\Users;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->full_name,
            'avatar' => $this->resource->img_url_path,
            'currency' => $this->resource->currency,
        ];
    }
}
