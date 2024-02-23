<?php

namespace App\Service\Timetrack;

use App\DayType;
use App\Models\WorkChart\WorkChartModel;
use App\Salary;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TimetrackService
{
    /**
     * @param array $salaryBonuses
     * @param array $obtainedBonuses
     * @param $userFines
     * @param int $finesSum
     * @param array $advances
     * @param User $user
     * @param Carbon $date
     * @param $currencyRate
     * @return array
     * @throws Exception
     */
    public function getUserFinalSalary(
        array  $salaryBonuses,
        array  $obtainedBonuses,
               $userFines,
        int    $finesSum,
        array  $advances,
        User   $user,
        Carbon $date,
               $currencyRate
    ): array
    {
        $advancesSum = collect($advances)->sum('paid');

        // Вычисление даты принятия
        $userAppliedAt = $user->applied_at();

        $timeTracking = $this->getUserTimetracking($user, $date->year, $date->month, $userAppliedAt, '>=');
        $traineeDays = $this->getDateTypes($date, $user);
        $timeTrackingBeforeApply = $this->getUserTimetracking($user, $date->year, $date->month, $userAppliedAt, '<');

        $data = $this->collectSalaryData(
            $date,
            $user,
            $timeTracking,
            $timeTrackingBeforeApply,
            $traineeDays,
            $currencyRate,
            $userFines,
            $salaryBonuses,
            $obtainedBonuses,
            $advances
        );

        return [
            'data' => [
                'salaries' => $data['salaries'],
                'times' => $data['times'],
                'hours' => $data['hours'],
                'taxes' => $user->taxes()
                    ->select('taxes.id', 'taxes.name', 'taxes.end_subtraction',
                        DB::raw('CASE WHEN user_tax.value > 0 THEN user_tax.value ELSE taxes.value END AS value'),
                        'taxes.is_percent')
                    ->wherePivot('created_at', '<=', $date->lastOfMonth()->format('Y-m-d'))
                    ->get()
            ],
            'totalFines' => $finesSum,
            'total_avanses' => $advancesSum
        ];
    }

    /**
     * @param User $user
     * @param int $year
     * @param int $month
     * @param string $user_applied_at
     * @param string $operator
     * @return Collection
     */
    private function getUserTimetracking(User $user, int $year, int $month, string $user_applied_at, string $operator): Collection
    {
        return Timetracking::select([
            DB::raw('DAY(enter) as date'),
            DB::raw('sum(total_hours) as total_hours'),
            DB::raw('MIN(enter) as enter')
        ])
            ->whereMonth('enter', $month)
            ->whereYear('enter', $year)
            ->whereDate('enter', $operator, Carbon::parse($user_applied_at)->format('Y-m-d'))
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->get();
    }

    /**
     * @param Carbon $date
     * @param User $user
     * @return Collection
     */
    private function getDateTypes(Carbon $date, User $user): Collection
    {
        return DayType::selectRaw("DAY(date) as datex")
            ->where('user_id', $user->id)
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->whereIn('type', [5, 6, 7])
            ->get(['datex']);
    }

    /**
     * @param Carbon $date
     * @param User $user
     * @param Collection $timeTracking
     * @param Collection $timeTrackingBeforeApply
     * @param Collection $traineeDays
     * @param $currencyRate
     * @return array[]
     */
    private function collectSalaryData(
        Carbon     $date,
        User       $user,
        Collection $timeTracking,
        Collection $timeTrackingBeforeApply,
        Collection $traineeDays,
                   $currencyRate,
                   $userFines,
                   $salaryBonuses,
                   $obtainedBonuses,
                   $avanses
    ): array
    {
        $data = [
            'salaries' => [],
            'times' => [],
            'hours' => [],
        ];

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $m = $i;
            if (strlen($i) == 1) $m = '0' . $i;
            $data['salaries'][$i]['fines'] = isset($userFines[$m]) ? $userFines[$m] : [];
            $data['salaries'][$i]['bonuses'] = isset($salaryBonuses[$m]) ? $salaryBonuses[$m] : [];
            $data['salaries'][$i]['awards'] = isset($obtainedBonuses[$m]) ? $obtainedBonuses[$m] : [];
            $data['salaries'][$i]['avanses'] = isset($avanses[$m]) ? $avanses[$m] : [];
        }

        for ($day = 1; $day <= $date->daysInMonth; $day++) {

            $s = Salary::where('user_Id', $user->id)
                ->where('date', $date->day($day)->format('Y-m-d'))
                ->first();

            $zarplata = $s ? $s->amount : 70000;
            $schedule = $user->schedule(true);
            $workChart = $user->workChart;

            // Проверяем установлена ли время отдыха
            if ($workChart && $workChart->rest_time != null) {
                $lunchTime = $workChart->rest_time;
                $hour = floatval($lunchTime / 60);
                $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
                $workingHours = round($userWorkHours / 3600, 1) - $hour;
            } else {
                $lunchTime = 1;
                $userWorkHours = max($schedule['end']->diffInSeconds($schedule['start']), 0);
                $workingHours = round($userWorkHours / 3600, 1) - $lunchTime;
            }

            // Проверяем тип рабочего графика, так как есть у нас недельный и сменный тип
            $workChartType = $workChart->work_charts_type ?? 0;
            if ($workChartType === 0 || $workChartType === WorkChartModel::WORK_CHART_TYPE_USUAL) {
                $ignore = $user->getCountWorkDays();   // Какие дни не учитывать в месяце
                $workdays = workdays($date->year, $date->month, $ignore);
            } elseif ($workChartType === WorkChartModel::WORK_CHART_TYPE_REPLACEABLE) {
                $workdays = $user->getCountWorkDaysMonth();
            } else {
                throw new Exception(message: 'Проверьте график работы', code: 400);
            }

            $hourlyPay = round($zarplata / $workdays / $workingHours, 2);

            // count
            $data['times'][$day]['value'] = $timeTracking->where('date', $day)->count() > 0 ? $timeTracking->where('date', $day)->first()->enter->format('H:i') : '';

            $hour = $timeTracking->where('date', $day)->count() > 0 ? $timeTracking->where('date', $day)->first()->total_hours / 60 : '';
            if ($hour < 0 || $hour == '') {
                $hour = 0;
            }

            $data['salaries'][$day]['value'] = number_format(round($hour * $hourlyPay * $currencyRate), 0, '.', '');
            $data['salaries'][$day]['training'] = false;

            if ($traineeDays->where('datex', $day)->first()) {
                $hour = $user->working_time_id == 1 ? 8 : 9;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourlyPay * $currencyRate * $user->internshipPayRate()), 0, '.', ''); // стажировочные на пол суммы
                $data['salaries'][$day]['training'] = true;
            } else if ($timeTrackingBeforeApply->where('date', $day)->first()) {
                $hour = $timeTrackingBeforeApply->where('date', $day)->first()->total_hours / 60;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourlyPay * $currencyRate), 0, '.', '');
            }

            $data['hours'][$day]['value'] = round($hour, 2);

            if ($data['salaries'][$day]['training'] || $data['hours'][$day]['value'] == 0) $data['hours'][$day]['value'] = '';
            if ($data['salaries'][$day]['value'] == 0) $data['salaries'][$day]['value'] = '';

            $data['salaries'][$day]['calculated'] = round($hour, 2) . ' * ' . ($traineeDays->where('datex', $day)->first() ? $hourlyPay * $user->internshipPayRate() : $hourlyPay);
        }
        return $data;
    }

}
