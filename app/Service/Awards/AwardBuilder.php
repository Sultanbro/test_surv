<?php

namespace App\Service\Awards;
use App\Service\Interfaces\Award\AwardBuilderInterface;

class AwardBuilder implements AwardBuilderInterface
{

    public function handle(string $typeName)
    {
        $method = 'build' . ucfirst($typeName);

        if (!method_exists($this, $method)) {
            throw new \Exception("Method $method not defined, please create");
        }

        return $this->{$method}();
    }

    protected function regressCourse(): CourseRegress
    {
        return new CourseRegress();
    }

}