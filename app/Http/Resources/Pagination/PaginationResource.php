<?php

namespace App\Http\Resources\Pagination;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property-read LengthAwarePaginator $resource
 */
class PaginationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'page' => $this->resource->currentPage(),
            'per_page' => $this->resource->perPage(),
            'last_page' => $this->resource->lastPage(),
            'total' => $this->resource->total(),
            'next_page_url' => $this->resource->nextPageUrl(),
        ];
    }
}
