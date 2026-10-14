<?php

namespace App\Http\Resources\Pagination;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property-read LengthAwarePaginator $resource
 */
class PaginationCollection extends ResourceCollection
{
    protected array $items;

    public function toArray($request): array
    {
//        return array_merge(
//            (new PaginationResource($this->resource))->toArray($request),
//            $this->items,
//        );

        return array_merge(
            ['pagination' => (new PaginationResource($this->resource))->toArray($request)],
            $this->items,
        );
    }
}
