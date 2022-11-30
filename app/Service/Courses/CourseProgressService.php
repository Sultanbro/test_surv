<?php

namespace App\Service\Courses;

use App\DTO\CourseProgressDTO;
use App\Exceptions\NotResultsException;
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
     * @throws NotResultsException
     */
    public function handle(): array
    {
        try {
            return [
                'course'      => $this->dto->course,
                'testResults' => $this->dto->user->test_results()->get()
            ];
        } catch (NotResultsException $exception) {
            Log::error($exception->getMessage());

            throw new NotResultsException($exception->getMessage());
        }
    }
}