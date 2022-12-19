<?php

namespace App\Filters\Persons;

use App\Repositories\UserRepository;
use App\Support\Interfaces\Filters\UserFilterBuilderInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter implements UserFilterBuilderInterface
{
    public function __construct(
        public UserRepository $userRepository
    )
    {}

    public function all(array $filters)
    {
        return $this->userRepository->userWithDescription(
            $filters['filter'],
            $filters['job'],
            $filters['start_date'] ?? null,
            $filters['end_date']  ?? null,
            $filters['start_date_deactivate']  ?? null,
            $filters['end_date_deactivate']  ?? null,
            $filters['start_date_applied']  ?? null,
            $filters['end_date_applied']  ?? null,
            $filters['segment'] ?? null,
            false
        );
    }

    public function deactivated(array $filters)
    {
        return $this->userRepository->userWithDescription(
            $filters['filter'],
            $filters['job'],
            null,
            null,
                $filters['start_date_deactivate']  ?? null,
            $filters['end_date_deactivate']  ?? null,
            null,
            null,
            $filters['segment'] ?? null
        );
    }

    public function nonFilled()
    {
        return $this->userRepository->userWithDownloads();
    }

    public function trainees(array $filters)
    {
        return $this->userRepository->getTrainees(
            $filters['job'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date']  ?? null,
            $filters['start_date_deactivate']  ?? null,
            $filters['end_date_deactivate']  ?? null
        );
    }

    public function active(array $filters)
    {
        return $this->userRepository->userWithDescription(
            $filters['filter'] ?? null,
            $filters['job'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date'] ?? null,
            null,
            null,
            $filters['start_date_applied'] ?? null,
                $filters['start_date_applied'] ?? null,
            $filters['segment'] ?? null,
            ''
        );
    }
}