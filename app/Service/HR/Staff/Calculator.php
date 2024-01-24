<?php

namespace App\Service\HR\Staff;

class Calculator
{
    const CALC_TYPES = [
        1 => 'total_staff_turnover_rate',
        2 => 'total_staff_of_fired',
        3 => 'trainee_turnover_rate',
    ];
    private string $type;
    private string $percent = '0%';

    public function calculate(array $staff, int $month): void
    {
        if (in_array($this->type, [self::CALC_TYPES[1], self::CALC_TYPES[3]])) {
            $this->percent = $this->total_staff_turnover_rate($staff[0]['m' . $month], $staff[1]['m' . $month]) . '%';
        }
    }

    public function type(int $type): void
    {
        $this->type = self::CALC_TYPES[$type];
    }

    public function percent(): string
    {
        return $this->percent;
    }

    private function total_staff_turnover_rate(int $fired, $active): float
    {
        return round(($fired / $active) * 100, 1);
    }
}