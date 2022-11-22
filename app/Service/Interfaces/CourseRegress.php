<?php

namespace App\Service\Interfaces;

use App\Http\Requests\CourseRegressRequest;

interface CourseRegress
{
    public function regress(array $data);
}