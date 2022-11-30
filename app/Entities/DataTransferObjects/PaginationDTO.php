<?php

namespace App\Entities\DataTransferObjects;

class PaginationDTO
{
    protected int $perPage;
    protected int $page;

    public function __construct(int $perPage, int $page)
    {
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }
}
