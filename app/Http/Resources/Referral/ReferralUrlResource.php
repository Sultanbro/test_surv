<?php

namespace App\Http\Resources\Referral;

use App\Service\Referral\Core\ReferralUrlDto;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ReferralUrlDto $resource
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
