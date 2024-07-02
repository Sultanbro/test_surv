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
        $group = $this->resource->inGroups()->toArray();
        $groupHead = $this->resource->inGroups(true)->toArray();

        return [
            'id' => $this->resource->id,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'birthday' => $this->resource->birthday,
            'name' => $this->resource->name,
            'last_name' => $this->resource->last_name,
            'avatar' => $this->resource->img_url_path,
            'position_id' => $this->resource->position?->id,
            'position_name' => $this->resource->position?->position,
            'profile_group' => array_merge($group, $groupHead),
            'deleted_at' => $this->resource->deleted_at,
            'last_seen' => $this->resource->last_seen,
            'referrer_status' => $this->resource->referrer_status,
        ];
    }
}
