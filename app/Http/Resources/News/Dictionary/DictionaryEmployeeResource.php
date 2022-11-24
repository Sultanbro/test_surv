<?php

namespace App\Http\Resources\News\Dictionary;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class DictionaryEmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->full_name,
            'last_name' => $this->resource->last_name,
            'avatar' => asset($this->resource->img_url_path),
            'position_name' => $this->resource->position->name,
            'profile_group' => $this->resource->profileGroups->map(fn($item) => $item->name)->toArray()
        ];
    }
}
