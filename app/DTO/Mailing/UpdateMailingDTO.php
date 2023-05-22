<?php
declare(strict_types=1);

namespace App\DTO\Mailing;

use App\DTO\BaseDTO;

final class UpdateMailingDTO extends BaseDTO
{
    /**
     * @param int $id
     * @param ?string $name
     * @param ?string $title
     * @param ?array $date
     * @param ?array<string> $typeOfMailing
     * @param ?bool $isTemplate
     * @param bool|null $status
     * @param int $count
     */
    public function __construct(
        public int $id,
        public ?string $name,
        public ?string $title,
        public ?array $date,
        public ?array $typeOfMailing,
        public ?bool $isTemplate,
        public ?bool $status,
        public int $count
    )
    {}

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'title'      => $this->title,
            'days'       => $this->date,
            'type_of_mailing' => $this->typeOfMailing,
            'is_template' => $this->isTemplate,
            'status'    => $this->status,
            'count'     => $this->count
        ];
    }
}