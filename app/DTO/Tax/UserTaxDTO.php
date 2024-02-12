<?php

namespace App\DTO\Tax;

class UserTaxDTO
{
    /**
     * @param int $taxId
     * @param int $userId
     * @param int|null $value
     * @param bool|null $isPercent
     * @param bool|null $endSubtraction
     */
    public function __construct(
        public int $taxId,
        public int $userId,
        public int|null $value,
        public bool|null $isPercent,
        public bool|null $endSubtraction,
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tax_id' => $this->taxId,
            'user_id' => $this->userId,
            'is_percent' => $this->isPercent,
            'end_subtraction' => $this->endSubtraction,
            'value' => $this->value,
        ];
    }
}
