<?php

namespace App\Repositories\Referral;

use App\DayType;
use App\Facade\Referring;
use App\Service\Referral\Core\PaidType;
use App\Service\Referral\SalaryFilter;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    private Carbon $startDate;
    private Carbon $endDate;

    public function __construct(
        private readonly SalaryFilter $salaryFilter
    )
    {
    }

    public function schedule(User $referrer, int $step = 1)
    {
        return $referrer->referrals()
            ->with(['user_description', 'referrals', 'referralSalaries'])
            ->orderBy("created_at")
            ->get()
            ->map(function (User $referral) use ($referrer, $step) {

                Referring::touchReferrerStatus($referral); // before get statistic, we check the user referrer status

                $days = $this->getReferralDayTypes($referral);

                $salaries = $this->getReferralSalaries($referrer, $referral);

                $this->salaryFilter->forThisCollection($salaries);

                $training = $this->salaryFilter->filter(PaidType::TRAINEE);
                $working = $this->salaryFilter->filter(PaidType::WORK);
                $attestation = $this->salaryFilter->filter(PaidType::ATTESTATION);

                $referral->is_trainee = $referral->user_description?->is_trainee;
                $referral->datetypes = array_merge(
                    $this->traineesDaily($days, $training),
                    $this->attestation($attestation),
                    $this->employeeWeekly($referral, $working)
                );

                if ($referral->referrals->count()) {

                    if ($step <= 3) {
                        $referral->users = $this->schedule($referral, $step + 1);
                    }
                }

                return $referral;
            });
    }

    public function setStartDate(Carbon $date): void
    {
        $this->startDate = $date;
    }

    public function setEndDate(Carbon $date): void
    {
        $this->endDate = $date;
    }

    private function getReferralSalaries(User $referrer, User $referral): Collection
    {
        return $referrer->referralSalaries()
            ->where([
                'referral_id' => $referral->getKey(),
                'referrer_id' => $referrer->getKey()
            ])->get();
    }

    private function traineesDaily(Collection $days, $training): array
    {
        $types = [];
        for ($i = 1; $i <= $this->startDate->daysInMonth; $i++) {
            $day = $this->getDay($days, $i);
            if ($this->isAbsence($day)) {
                $types[$i] = null;
            } elseif ($this->isTrainee($day)) {
                $types[$i] = $this->countTrainingDays($training, $day);
            }
        }
        return $types;
    }

    private function attestation($attestation): array
    {
        $appliedSalary = current($attestation->toArray());

        if (!$appliedSalary) {
            return [];
        }

        return [
            'pass_certification' => $this->parseSalary($appliedSalary)
        ];
    }

    private function employeeWeekly(User $referral, Collection $working): array
    {
        $weeksToTrack = [1, 2, 3, 4, 6, 8, 12];
        $weekTemplate = $this->createWeekTemplate($weeksToTrack);
        $timeTracking = $this->getReferralTimeTracking($referral, $this->startDate);

        foreach ($timeTracking as $tracker) {
            for ($week = 1; $week <= $weeksToTrack; $week++) {
                // If the week is beyond 12, exit the loop
                if ($week > 12) {
                    break;
                }

                // Check if the current week is in the weeks to track
                if (in_array($week, $weeksToTrack)) {
                    $current = [];

                    // Find the working item with the same date as the exit date
                    foreach ($working as $item) {
                        if ($this->isSameDate(Carbon::parse($item['date']), $tracker->exit)) {
                            $current = $item->toArray();
                            break; // Exit the loop once found
                        }
                    }

                    // Store the parsed salary in the result array
                    $weekTemplate[$week . '_week'] = $this->parseSalary($current);
                }
            }
        }

        return $weekTemplate;
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

    private function getReferralDayTypes(User $referral): Collection
    {
        return DayType::query()
            ->selectRaw("*,DATE_FORMAT(date, '%e') as day")
            ->where('user_id', $referral->getKey())
            ->whereMonth('date', '=', $this->startDate->month)
            ->whereYear('date', $this->startDate->year)
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

    private function getDay(Collection $days, int $day): ?DayType
    {
        /** @var DayType $day */
        return $days
            ->where('day', $day)
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

        return $this->parseSalary($salary);
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

    private function parseSalary(?array $current): array
    {
        return [
            'paid' => (bool)($current['is_paid'] ?? null),
            'sum' => $current['amount'] ?? null,
            'comment' => $current['comment'] ?? null,
            'id' => $current['id'] ?? null,
        ];
    }
}