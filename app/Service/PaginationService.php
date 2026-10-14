<?php

namespace App\Service;

use App\Entities\DataTransferObjects\PaginationDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationService
{
    public function paginate($builder, PaginationDTO $paginationDTO): LengthAwarePaginator
    {
        return $builder->paginate($paginationDTO->getPerPage());
    }
}
