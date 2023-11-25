<?php
declare(strict_types=1);

namespace App\DTO\Analytics\V2;
use Webmozart\Assert\Assert;

final class SpeedometerDto
{
    /**
     * @param array $gauge
     * @param int $type
     */
    public function __construct(
        public readonly array $gauge,
        public readonly int $type
    )
    {}

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(
        array $data
    ): self
    {
        Assert::keyExists($data, 'gauge');
        Assert::keyExists($data, 'type');

        return new self($data['gauge'], $data['type']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'activity_id'   => $this->gauge['activity_id'],
            'value'         => $this->gauge['value'],
            'name'          => $this->gauge['name'],
            'value_type'    => $this->gauge['value_type'],
            'round'         => $this->gauge['round'],
            'is_main'       =>$this->gauge['is_main'],
            'min_value'     => $this->gauge['min_value'],
            'cell'          => $this->gauge['cell'],
            'max_value'     => $this->gauge['max_value'],
            'reversed'      => $this->gauge['reversed'],
            'options'       => json_encode($this->gauge['options']),
            'unit'          => $this->gauge['unit'] ?: '',
            'group_id'      => $this->gauge['group_id'],
            'date'          => $this->gauge['date'],
            'type'          => $this->type
        ];
    }
}