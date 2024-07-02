<?php

namespace App\Service\Referral;

use App\Models\Referral\ReferralSalary;
use App\Service\Referral\Core\PaidType;
use Illuminate\Database\Eloquent\Collection;

class SalaryFilter
{
    /** @var Collection<ReferralSalary> $salaries */
    private Collection $salaries;

    public function for(Collection $collection): void
    {
        $this->salaries = $collection;
    }

    public function filter(PaidType $type): Collection
    {
        return $this->salaries->filter(fn(ReferralSalary $salary) => $salary->type === $type);
    }
}