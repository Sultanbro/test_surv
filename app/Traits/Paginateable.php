<?php

namespace App\Traits;


use App\Entities\DataTransferObjects\PaginationDTO;

trait Paginateable
{
    protected int $defaultPage;
    protected int $defaultPerPage;

    public function withPaginationRules(array $rules): array
    {
        return array_merge($rules, [
            'per_page' => ['integer',],
            'page' => ['integer',],
        ]);
    }

    public function getPagination(): PaginationDTO
    {
        return new PaginationDTO(
            $this->input('per_page', $this->defaultPerPage),
            $this->input('page', $this->defaultPage),
        );
    }
}
