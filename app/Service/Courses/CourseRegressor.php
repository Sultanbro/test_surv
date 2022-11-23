<?php

namespace App\Service\Courses;

use App\Service\Interfaces\CourseRegressor as RegressorInterface;
use Exception;

class CourseRegressor implements RegressorInterface
{
    /**
     * @throws Exception
     */
    public function handle(string $typeName)
    {
        $method = 'regress' . ucfirst($typeName);

        if (!method_exists($this, $method)) {
            throw new \Exception("Method $method not defined, please create");
        }

        return $this->{$method}();
    }

    protected function regressCourse(): CourseRegress
    {
        return new CourseRegress();
    }

    protected function regressItem(): CourseItemRegress
    {
        return new CourseItemRegress();
    }
}