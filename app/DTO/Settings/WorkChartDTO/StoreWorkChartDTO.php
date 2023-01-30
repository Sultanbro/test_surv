<?php
declare(strict_types=1);

namespace App\DTO\Settings\WorkChartDTO;


final class StoreWorkChartDTO
{
    /**
     * @param string $name
     * @param string $timeBeg
     * @param string $timeEnd
     * @param array $dayOff
     */
    public function __construct(
        public string $name,
        public string $timeBeg,
        public string $timeEnd,
        public array $dayOff,
    )
    {}

    /**
     * @return array
     */
    public
    function toArray(): array
    {
        return [
            'name' => $this->name,
            'time_beg' => $this->timeBeg,
            'time_end' => $this->timeEnd,
            'day_off' => $this->dayOff
        ];
    }
}