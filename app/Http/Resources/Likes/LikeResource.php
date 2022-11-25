<?php

namespace App\Http\Resources\Likes;

use App\Helpers\DateHelper;
use App\Http\Resources\Users\UserResource;
use App\Models\Like;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Like $resource
 */
class LikeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => (new UserResource($this->resource->user))->toArray($request),
            'created_at' => DateHelper::format($this->resource->created_at),
        ];
    }
}
