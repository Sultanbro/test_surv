<?php

namespace App\Service\Timetrack\SalaryWorkChart;

use App\Classes\Helpers\Currency;
use App\DTO\TimeTrack\SalaryWorkChartDTO\SalaryWorkChartDTO;
use App\Models\WorkChart\WorkChartModel;
use App\Salary;
use App\Service\Bonus\ObtainedBonusService;
use App\Service\Bonus\TestBonusService;
use App\Service\Fine\FineService;
use App\Service\Salary\SalaryService;
use App\Service\Timetrack\TimetrackService;
use App\Timetracking;
use App\User;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
* Класс для работы с Service.
*/
class SalaryWorkChartService
{
    public function __construct(
        public TimetrackService $timetrackService,
        public FineService $fineService,
        public SalaryService $salaryService,
        public ObtainedBonusService $obtainedBonusesService,
        public TestBonusService $testBonusService,
    )
    {}

    public function salaryBalance(SalaryWorkChartDTO $dto)
    {
        $user = auth()->user() ?? User::find(23368);
        $carbonDate = Carbon::createFromDate($dto->year, $dto->month, 1);
        $daysInMonth = Carbon::createFromDate($dto->year, $dto->month, 1)->daysInMonth;
        $userWorkChart = WorkChartModel::find($user->working_day_id);
        $dayWork = substr($userWorkChart->name,0,1);
        $dayOff = substr($userWorkChart->name,2);
        $startDay = (int)$dto->start_day;
        $hollidays = json_decode($dto->hollidays, true);
        $userAppliedAt = $user->applied_at();
        $timeTracking = $this->getUserTimetracking($user, $dto->year, $dto->month, $userAppliedAt, '>=');
        $toDay = Carbon::parse(now())->day;
        $hour = $timeTracking->where('date', $toDay)->count() > 0 ? $timeTracking->where('date', $toDay)->first()->total_hours / 60 : '';
        if ($startDay < $daysInMonth && $startDay != 0 && $dayOff != 0 && $dayWork) {
            if(!is_null($hollidays)){
                $charts = $this->restHoliday($carbonDate,$startDay,$daysInMonth,$hollidays,$dayOff,$dayWork);
            }else{
                $charts = $this->otherCharts($carbonDate,$startDay,$daysInMonth,$dayOff,$dayWork);
            }
        }else{
            $charts = 'Вы указали не существующий день в выбранном месяце';
        }
        $countDayWork = $this->countDayWork($charts);
        $countDayOff = $this->countDayOff($charts);
        $result = $this->calculateSalary($hour,$toDay,$charts,$carbonDate,$countDayWork,$dto,$user,$startDay,);
        $arResult = [
            'work_chart' => $result,
            'chart_shift' => $dayWork.'/'.$dayOff,
            'count_day_work' => $countDayWork,
            'count_day_off' => $countDayOff,
            'holidays' => ($hollidays != null) ? $hollidays : 'по графику',
            'total_salary' => $this->totalSalaryToDay($result),
            'total_hours' => $timeTracking[0]->total_hours/60,
        ];
        return $arResult;
    }

    public function calculateSalary($hour,$toDay,$charts,$carbonDate,$countDayWork,$dto,$user)
    {

        $userSalary = Salary::where('user_id',$user->id)->first();
        $salaryAmount = $userSalary->amount ?: 70000;
        $userWorkChart = WorkChartModel::find($user->working_day_id);
        $workingHours = (int)$userWorkChart->time_end - (int)$userWorkChart->time_beg;
        $hourlyPay = round($salaryAmount / $countDayWork / $workingHours, 2);
        $currency_rate = (float)(Currency::rates()[$user->currency] ??  0.00001);
        $userTotalFines = $this->fineService->getUserFines($dto->month, $user, $currency_rate);
        $salaryBonuses = $this->salaryService->getUserBonuses($carbonDate, $user);
        $obtainedBonuses = $this->obtainedBonusesService->getUserBonuses($carbonDate,$user);
        $testBonuses =  $this->testBonusService->getUserBonuses($carbonDate,$user);
        $advances = $this->salaryService->getUserAdvances($carbonDate, $user);

        foreach ($charts as $elem){
            if($elem['day_off'] != true && $elem['day'] <= $toDay){
                $salaries[] = array_merge($elem,['salary' => number_format(round($hour * $hourlyPay * $currency_rate),0,'.','')],
                    ['bonuses' => number_format(round(collect($salaryBonuses)->sum('paid')),0,'.','')],
                    ['fines' => number_format(round($userTotalFines['total']),0,'.','')],
                    ['awards' => number_format(round(collect($obtainedBonuses)->sum('paid')),0,'.','')],
                    ['test_bonus' => number_format(round(collect($testBonuses)->sum('paid')),0,'.','')],
                    ['avanses' => number_format(round(collect($advances)->sum('paid')),0,'.','')],
                    ['time_start' => substr($user->work_start,0,5)]);
            }else{
                $salaries[] = $elem;
            }
        }

        return $salaries;
    }

    public function totalSalaryToDay($salaries)
    {
        foreach ($salaries as $elem){
            if(isset($elem['salary'])){
                $sumSalary[] = $elem['salary'];
            }
        }
        return array_sum($sumSalary);
    }

    public function total_hours($salaries)
    {
        foreach ($salaries as $elem){
            if(isset($elem['salary'])){
                $sumSalary[] = $elem['salary'];
            }
        }
        return array_sum($sumSalary);
    }

    public function countDayWork($charts)
    {
        foreach ($charts as $elem){
            if($elem['day_off'] != true){
                $countDayWork[] = $elem['day_off'];
            }
        }
        return count($countDayWork);
    }

    public function countDayOff($charts)
    {
        foreach ($charts as $elem){
            if($elem['day_off'] == true){
                $countDayOff[] = $elem['day_off'];
            }
        }
        return count($countDayOff);
    }

    public function getWeek()
    {
        return [
            1 => 'Пн',
            2 => 'Вт',
            3 => 'Ср',
            4 => 'Чт',
            5 => 'Пт',
            6 => 'Сб',
            0 => 'Вс',
        ];
    }

    public function restHoliday($date,$startDay,$daysInMonth,$hollidays)
    {
        $week = $this->getWeek();
        $arMonthDay = [];

        for ($i = $startDay; $i <= $daysInMonth; $i++) {
                if (!empty($hollidays)) {
                    $dayItem = getDate(strtotime($i . strstr($date->format('d.m.Y'), '.')))['wday'];
                    if (isset($hollidays[$dayItem])) {
                        $arMonthDay[$i] = [
                            'day_name' => $hollidays[$dayItem] ?? '',
                            'day' => $i,
                            'day_off' => true,
                        ];
                    } else {
                        $arMonthDay[$i] = [
                            'day_name' => $week[$dayItem] ?? '',
                            'day' => $i,
                            'day_off' => false,
                        ];
                    }
                }
        }
        return $arMonthDay;
    }

    public function otherCharts($carbonDate,$startDay,$daysInMonth,$dayOff,$dayWork)
    {
        $week = $this->getWeek();
        $dayCount = 1;
        $dayCountOff = 1;
        $arMonthDay = [];
        for ($i = $startDay; $i <= $daysInMonth; $i++) {
                if ($dayCount > $dayWork) {
                    $arMonthDay[$i] = [
                        'day_name' => $week[getDate(strtotime($i . strstr($carbonDate->format('d.m.Y'), '.')))['wday']] ?? '',
                        'day' => $i,
                        'day_off' => true,
                    ];
                    if ($dayCountOff < $dayOff) {
                        for ($k = 1; $k < $dayOff; $k++) {
                            if ($i + $k <= $daysInMonth)
                                $arMonthDay[$i + $k] = [
                                    'day_name' => $week[getDate(strtotime($i + $k . strstr($carbonDate->format('d.m.Y'), '.')))['wday']] ?? '',
                                    'day' => $i + $k,
                                    'day_off' => true,
                                ];
                        }
                    }
                    $dayCount = 1;
                    $dayCountOff = 1;
                    continue;
                }
                    if (!isset($arMonthDay[$i])) {
                        $arMonthDay[$i] = [
                            'day_name' => $week[getDate(strtotime($i . strstr($carbonDate->format('d.m.Y'), '.')))['wday']] ?? '',
                            'day' => $i,
                            'day_off' => false,
                        ];
                        $dayCount++;
                    }
        }
        return $arMonthDay;
    }

    /**
     * @param User $user
     * @param int $year
     * @param int $month
     * @param string $user_applied_at
     * @param string $operator
     * @return Collection
     */
    private function getUserTimetracking(User $user, int $year, int $month, string $user_applied_at, string $operator ): Collection{
        return Timetracking::select([
            DB::raw('DAY(enter) as date'),
            DB::raw('sum(total_hours) as total_hours'),
            DB::raw('MIN(enter) as enter')
        ])
            ->whereMonth('enter', $month)
            ->whereYear('enter', $year)
            ->whereDate('enter', $operator, \Carbon\Carbon::parse($user_applied_at)->format('Y-m-d'))
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->get();
    }
}