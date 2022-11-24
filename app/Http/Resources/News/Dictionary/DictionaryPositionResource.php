<?php

namespace App\Http\Resources\News\Dictionary;

use App\Position;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Position $resource
 */
class DictionaryPositionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'employees' => DictionaryEmployeeResource::collection($this->resource->employees)->toArray($request)
        ];
    }
}
