<?php
declare(strict_types=1);

namespace App\DTO\Mailing;

use App\DTO\BaseDTO;

final class CreateMailingDTO extends BaseDTO
{
    /**
     * @param string $title
     * @param array $recipients
     * @param string $frequency
     */
    public function __construct(
        public string $title,
        public array $recipients,
        public string $frequency
    )
    {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title'      => $this->title,
            'recipients' => $this->recipients,
            'frequency'  => $this->frequency
        ];
    }
}