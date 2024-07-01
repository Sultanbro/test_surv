<?php
declare(strict_types=1);

namespace App\DTO\Courses;

use App\DTO\BaseDTO;

final class UpdateDealDTO extends BaseDTO
{
    /**
     * @param int $deal_id
     * @param array $fields
     */
    public function __construct(
        public int $deal_id,
        public array $fields
    )
    {
    }

    /**
     * @return array[]
     */
    public function toArray(): array
    {
        return [
            'deal_id' => $this->deal_id,
            'fields' => $this->fields
        ];
    }
}