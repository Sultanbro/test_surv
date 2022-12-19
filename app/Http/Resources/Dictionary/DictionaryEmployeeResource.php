<?php

namespace App\Http\Resources\Dictionary;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class DictionaryEmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        $group = $this->resource->inGroups()->first();
        if (!$group){
            $group = $this->resource->inGroups(true)->first();
        }
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'last_name' => $this->resource->last_name,
            'avatar' => $this->resource->img_url_path,
            'position_id' => $this->resource->position?->id,
            'position_name' => $this->resource->position?->position,
            'profile_group' => $group
        ];
    }
}
