<?php
declare(strict_types=1);

namespace App\DTO\Courses;

use App\DTO\BaseDTO;

final class GetCourseItemResult extends BaseDTO
{
    /**
     * @param array $ids
     */
    public function __construct(
        public array $ids
    )
    {
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            'ids' => $this->ids
        ];
    }
}