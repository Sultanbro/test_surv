<?php

namespace App\Service\Awards\AwardTypes;

use App\Http\Requests\Award\StoreAwardRequest;
use App\Http\Requests\Award\UpdateAwardRequest;
use App\Models\Award\Award;
use App\Models\Course;
use App\Models\CourseResult;
use App\Service\Interfaces\Award\AwardInterface;
use App\User;
use Symfony\Component\HttpFoundation\Response;

class CertificateAwardService implements AwardInterface
{

    public function fetch(array $data): array
    {

    }

    public function store(StoreAwardRequest $request)
    {

    }

    public function update(UpdateAwardRequest $request)
    {

    }
    public function updateCourses($courseIds, $award){

    }
    /**
     * @param $request
     * @return array
     */
    private function saveAwardFile($request): array
    {

    }



}