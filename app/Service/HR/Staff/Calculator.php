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

    public function calculate()
    {

    }

    public function type(int $type): void
    {
        $this->type = self::CALC_TYPES[$type];
    }
}