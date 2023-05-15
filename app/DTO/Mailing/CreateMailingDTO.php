<?php
declare(strict_types=1);

namespace App\DTO\Mailing;

use App\DTO\BaseDTO;

final class CreateMailingDTO extends BaseDTO
{
    /**
     * @param string $name
     * @param string $title
     * @param array $recipients
     * @param array $date
     * @param array<string> $typeOfMailing
     * @param ?bool $isTemplate
     */
    public function __construct(
        public string $name,
        public string $title,
        public array $recipients,
        public array $date,
        public array $typeOfMailing,
        public ?bool $isTemplate
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
            'recipients' => $this->recipients,
            'days'       => $this->date,
            'type_of_mailing' => $this->typeOfMailing,
            'is_template' => $this->isTemplate
        ];
    }
}