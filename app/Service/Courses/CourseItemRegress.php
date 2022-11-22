<?php

namespace App\Service\Courses;

use App\Http\Requests\CourseRegressRequest;
use App\Service\Interfaces\CourseRegress;

/**
 * Класс CourseRegress отвечает за обнуление бонусов для разделов курса.
 */
class CourseItemRegress implements CourseRegress
{
    public function regress(array $data)
    {

    }
}