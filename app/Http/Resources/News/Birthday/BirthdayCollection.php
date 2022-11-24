<?php

namespace App\Http\Resources\News\Birthday;


use App\Http\Resources\Pagination\PaginationCollection;

class BirthdayCollection extends PaginationCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->items = [
            'birthdays' => BirthdayResource::collection($this->resource)
        ];
    }
}
