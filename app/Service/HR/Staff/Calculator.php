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
    private string $percent;

    public function calculate(array $staff, int $month): void
    {
        $a = $month != 1 ? $staff[4]['m' . ($month - 1)] + $staff[0]['m' . $month] : 0;
        $this->percent = $a > 0 ? round(($staff[1]['m' . $month] / $a) * 100, 1) . '%' : '0%';
    }

    public function type(int $type): void
    {
        $this->type = self::CALC_TYPES[$type];
    }

    public function percent(): string
    {
        return $this->percent;
    }
}