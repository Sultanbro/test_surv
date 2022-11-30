<?php

namespace App\Http\Resources\Birthday;

use App\Helpers\DateHelper;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class BirthdayResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->full_name,
            'avatar' => $this->resource->img_url_path,
            'date_human' => DateHelper::prepareDate($this->resource->birthday),
            'date' => Carbon::parse($this->resource->birthday)->format('m-d-Y')
        ];
    }
}
