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
     * @param string $time
     * @param array<string> $typeOfMailing
     */
    public function __construct(
        public string $name,
        public string $title,
        public array $recipients,
        public array $date,
        public string $time,
        public array $typeOfMailing
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
            'time'       => $this->time,
            'type_of_mailing' => $this->typeOfMailing
        ];
    }
}