<?php

namespace App\Http\Resources\Referral;

use App\Service\Referral\Core\EarnedStatisticDto;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property EarnedStatisticDto $resource
 */
class UserEarnedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable
     */
    public function toArray($request): array|Arrayable
    {
        return $this->resource->toArray();
    }
}
