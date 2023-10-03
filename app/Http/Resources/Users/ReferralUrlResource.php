<?php

namespace App\Http\Resources\Users;

use App\Service\Referral\Core\ReferralDto;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ReferralDto $resource
 */
class ReferralUrlResource extends JsonResource
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
