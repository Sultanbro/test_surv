<?php

namespace App\Service\Referral;

use App\DayType;
use App\Service\Referral\Core\PaidType;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Scheduler
{
    protected array $filter = [];

    public function __construct(
        private readonly SalaryFilter $salaryFilter
    )
    {
    }

    protected function dateStart(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->startOfMonth()
            ->copy();
    }

    protected function dateEnd(): Carbon
    {
        $this->filter['date'] = $this->filter['date'] ?? now()->format("Y-m-d");
        return Carbon::parse($this->filter['date'])
            ->endOfMonth()
            ->copy();
    }

    public function setFilter(array $filter = []): void
    {
        $this->filter = $filter;
    }

    public function schedule(User $referrer, int $step = 1): void
    {
        $referrer->referrals->each(function (User $referral) use ($step, $referrer) {
            $days = $referral->daytypes()->where(function (Builder $query) {
                $query->where('type', DayType::DAY_TYPES['TRAINEE']);
                $query->whereMonth('date', '=', $this->dateStart()->month);
                $query->whereYear('date', $this->dateStart()->year);
            })->get();

            $referral->is_trainee = $referral->user_description?->is_trainee;
            $salaries = $referrer->referralSalaries->where('referral_id', $referral->getKey());
            $this->salaryFilter->forThisCollection($salaries);

            $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION)->first();
            $training = $this->salaryFilter->filter(PaidType::TRAINEE);
            $working = $this->salaryFilter->filter(PaidType::WORK);

            $referral->daytypes = array_merge(
                $this->traineesDaily($days, $training),
                $this->attestation($attestation),
                $this->employeeWeekly($referral, $working)
            );

            if ($referral->referrals_count) {
                if ($step <= 3) {
                    $this->schedule($referral, $step + 1);
                }
            }
        });
    }

    private function traineesDaily($days, $training): array
    {
        $types = [];
        for ($i = 1; $i <= $this->dateStart()->daysInMonth + 1; $i++) {
            $day = $this->getDay($days, $this->dateStart()->setDay($i));
            if (!$day) $types[$i] = null;
            if ($day) $types[$i] = $this->countTrainingDays($training, $day);
        }
        return $types;
    }

    private function attestation($attestation): array
    {
        if (!$attestation) {
            return [];
        }

        return [
            'pass_certification' => $attestation->toArray()
        ];
    }

    private function employeeWeekly(User $referral, Collection $salaries): array
    {
        $weeksToTrack = [1, 2, 3, 4, 6, 8, 12];
        $template = $this->createWeekTemplate($weeksToTrack);
        $timeTracking = $this->getReferralTimeTracking($referral, $this->dateStart());
        foreach ($timeTracking as $workedDay) {
            foreach ($salaries as $salary) {
                foreach ($weeksToTrack as $week) {
                    if ($this->isSameDate($salary->date, $workedDay->exit)) {
                        $template[$week . '_week'] = $salary->toArray();
                        break;
                    }
                }
            }
        }

        return $template;
    }

    private function getReferralTimeTracking(User $referral, Carbon $date): Collection
    {
        return Timetracking::query()
            ->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
            ->where('user_id', $referral->getKey())
            ->whereMonth('enter', '=', $date->month)
            ->whereYear('enter', $date->year)
            ->orderBy('id', 'ASC')
            ->get();
    }

    private function isAbsence(?DayType $day): bool
    {
        return is_null($day) || $day->type == DayType::DAY_TYPES['ABCENSE'];
    }

    private function isTrainee(?DayType $day): bool
    {
        if (!$day) {
            return false;
        }
        return in_array($day->type, [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']]);
    }

    private function getDay($days, string $day): ?DayType
    {
        /** @var DayType $day */
        return $days
            ->where('date', $day)
            ->first();
    }

    private function countTrainingDays($training, DayType $day): ?array
    {
        $salary = [];
        foreach ($training as $item) {
            if ($this->isSameDate(Carbon::parse($item['date']), $day->date)) {
                $salary = $item->toArray();
            }
        }

        if (!count($salary)) {
            return null;
        }

        return $salary;
    }

    private function isSameDate(Carbon $first, Carbon $second): bool
    {
        return Carbon::parse($first)->format("Y-m-d") == $second->format("Y-m-d");
    }

    private function createWeekTemplate(array $weeks): array
    {
        $types = [];

        foreach ($weeks as $week) {
            $types[$week . '_week'] = null;
        }
        return $types;
    }
}