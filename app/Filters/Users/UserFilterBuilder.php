<?php
declare(strict_types=1);

namespace App\Filters\Users;

use App\Repositories\UserRepository;
use App\Support\Interfaces\Filters\UserFilterBuilderInterface;
use Illuminate\Database\Eloquent\Builder;

final class UserFilterBuilder
{
    private UserFilterBuilderInterface $filterBuilder;

    public function setBuilder(UserFilterBuilderInterface $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    public function getFilter(array $filters): object
    {
        switch ($filters['filter']) {
            case 'all':
                $filter = $this->getAll($filters);
                break;
            case 'deactivated':
                $filter = $this->getDeactivated($filters);
                break;
            case 'nonfilled':
                $filter = $this->getNonFilled();
                break;
            case 'trainees':
                $filter = $this->getTrainees($filters);
                break;
            case 'active':
                $filter = $this->getActive($filters);
                break;
        }

        return $filter->get();
    }

    /**
     * @param array $filters
     * @return object
     */
    private function getAll(
        array $filters
    ): object
    {
        return $this->filterBuilder->all($filters);
    }

    /**
     * @param array $filters
     * @return object
     */
    private function getDeactivated(
        array $filters
    ): object
    {
        return $this->filterBuilder->deactivated($filters);
    }

    /**
     * @return object
     */
    private function getNonFilled(): object
    {
        return $this->filterBuilder->nonFilled();
    }

    /**
     * @param array $filters
     * @return object
     */
    private function getTrainees(
        array $filters
    ): object
    {
        return $this->filterBuilder->trainees($filters);
    }

    /**
     * @param array $filters
     * @return object
     */
    private function getActive(
        array $filters
    ): object
    {
        return $this->filterBuilder->active($filters);
    }
}