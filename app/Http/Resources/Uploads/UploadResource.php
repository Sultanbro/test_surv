<?php

namespace App\Http\Resources\Uploads;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $resource
 */
class UploadResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'url' => $this->resource,
        ];
    }
}
