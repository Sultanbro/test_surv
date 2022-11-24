<?php

namespace App\Http\Resources\News\Dictionary;

use App\ProfileGroup;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProfileGroup $resource
 */
class DictionaryProfileGroupResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'avatar' => asset($this->resource->image_path),
            'employees' => DictionaryEmployeeResource::collection($this->resource->users)->toArray($request)
        ];
    }
}
