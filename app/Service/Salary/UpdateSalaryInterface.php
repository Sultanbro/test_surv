<?php

namespace App\Service\Salary;

interface UpdateSalaryInterface
{
    public function touch(string $date, int $groupId = null): void;
}