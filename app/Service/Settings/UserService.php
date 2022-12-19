<?php

namespace App\Service\Settings;

use App\Filters\Persons\UserFilter;
use App\Filters\Persons\UserFilterBuilder;

/**
* Класс для работы с Service.
*/
class UserService
{
    public function __construct(
        public UserFilterBuilder $builder,
        public UserFilter $filter
    )
    {}

    public function get(
        array $filters
    )
    {
        $this->builder->setBuilder($this->filter);
        return $this->builder->getFilter($filters);

    }
}