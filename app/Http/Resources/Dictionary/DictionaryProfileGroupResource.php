<?php

namespace App\Http\Resources\Dictionary;

use App\ProfileGroup;
use App\User;
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
            'active' => $this->resource->active,
//            'employees' => DictionaryEmployeeResource::collection($this->resource->profileGroupUsers)
//                ->toArray($request)
        ];
    }
}
