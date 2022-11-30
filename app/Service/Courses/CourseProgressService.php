<?php

namespace App\Service\Courses;

use App\DTO\CourseProgressDTO;
use Exception;
use Illuminate\Support\Facades\Log;

/**
* Класс для работы с Service.
*/
class CourseProgressService
{

    public function __construct(public CourseProgressDTO $dto)
    {

    }

    /**
     * @return array
     * @throws Exception
     */
    public function handle(): array
    {
        try {
            return [
                'course'      => $this->dto->course,
                'testResults' => $this->dto->user->test_results()->get()
            ];
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            throw new \Exception($exception->getMessage());
        }
    }
}