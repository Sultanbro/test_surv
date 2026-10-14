<?php

namespace App\Http\Resources\Referral;

use App\Service\Referral\Core\StatisticDto;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property StatisticDto $resource
 */
class StatisticResource extends JsonResource
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
