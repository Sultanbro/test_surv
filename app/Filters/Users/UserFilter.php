<?php
declare(strict_types=1);

namespace App\Filters\Users;

use App\Repositories\UserRepository;
use App\Support\Interfaces\Filters\UserFilterBuilderInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class UserFilter implements UserFilterBuilderInterface
{
    public function __construct(
        public UserRepository $userRepository
    )
    {}

    /**
     * @param array $filters
     * @return object
     */
    public function all(array $filters): object
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

    /**
     * @param array $filters
     * @return object
     */
    public function deactivated(array $filters): object
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

    /**
     * @return object
     */
    public function nonFilled(): object
    {
        return $this->userRepository->userWithDownloads();
    }

    /**
     * @param array $filters
     * @return object
     */
    public function trainees(array $filters): object
    {
        return $this->userRepository->getTrainees(
            $filters['job'] ?? null,
            $filters['start_date'] ?? null,
            $filters['end_date']  ?? null,
            $filters['start_date_deactivate']  ?? null,
            $filters['end_date_deactivate']  ?? null
        );
    }

    /**
     * @param array $filters
     * @return object
     */
    public function active(array $filters): object
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