<?php

namespace App\Service\Timetrack\SalaryWorkChart;

use App\Classes\Helpers\Currency;
use App\DTO\TimeTrack\SalaryWorkChartDTO\SalaryWorkChartDTO;
use App\Models\WorkChartModel;
use App\Salary;
use App\Service\Bonus\ObtainedBonusService;
use App\Service\Bonus\TestBonusService;
use App\Service\Fine\FineService;
use App\Service\Salary\SalaryService;
use App\Service\Settings\WorkChartService\WorkChartService;
use App\Service\Timetrack\TimetrackService;
use App\User;
use Illuminate\Support\Carbon;

/**
* Класс для работы с Service.
*/
class SalaryWorkChartService
{
    public function __construct(
        public WorkChartService $chartService,
        public TimetrackService $timetrackService,
        public FineService $fineService,
        public SalaryService $salaryService,
        public ObtainedBonusService $obtainedBonusesService,
        public TestBonusService $testBonusService,
    )
    {}

    public function salaryBalance(SalaryWorkChartDTO $dto,$carbonDate)
    {

        $user = auth()->user() ?? User::find(22709);
        $date  = Carbon::createFromDate($dto->year, $dto->month, 1)->format('d.m.Y');
        $daysInMonth = Carbon::createFromDate($dto->year, $dto->month, 1)->daysInMonth;
        $userWorkChart = WorkChartModel::find($user->working_day_id);
        $dayWork = substr($userWorkChart->name,0,1);
        $dayOff = substr($userWorkChart->name,2);
        $startDay = (int)$dto->start_day;
        $hollidays = json_decode($dto->hollidays, true);

        if ($startDay < $daysInMonth && $startDay != 0 && $dayOff != 0 && $dayWork) {
            $workChart = $this->createWorkingChart($date,$startDay,$daysInMonth,$hollidays,$dayOff,$dayWork,$dto,$carbonDate,$user);
        }else{
            $workChart = 'Вы указали не существующий день в выбранном месяце';
        }
        return $workChart;
    }

    public function createWorkingChart($date,$startDay,$daysInMonth,$hollidays,$dayOff,$dayWork,$dto,$carbonDate,$user)
    {
        if(!is_null($hollidays)){
            $charts = $this->restHoliday($date,$startDay,$daysInMonth,$hollidays,$dayOff,$dayWork);
        }else{
            $charts = $this->otherCharts($date,$startDay,$daysInMonth,$dayOff,$dayWork);
        }

        $countDayWork = $this->countDayWork($charts);
        $countDayOff = $this->countDayOff($charts);
        $result = $this->calculateSalary($charts,$countDayWork,$dto,$carbonDate,$user,$startDay);
        $arResult = [
            'График' => $result,
            'Смена' => $dayWork.'/'.$dayOff,
            'Кол-во рабочих дней' => $countDayWork,
            'Кол-во выходных дней' => $countDayOff,
            'Выходные' => ($hollidays != null) ?: 'по графику',
        ];
        return $arResult;
    }

    public function calculateSalary($arMonthDay,$countDayWork,$dto,$carbonDate,$user)
    {;
        $currency_rate = (float)(Currency::rates()[$user->currency] ??  0.00001);
        $userTotalFines = $this->fineService->getUserFines($dto->month, $user, $currency_rate);
        $salaryBonuses = $this->salaryService->getUserBonuses($carbonDate, $user);
        $obtainedBonuses = $this->obtainedBonusesService->getUserBonuses($carbonDate,$user);
        $testBonuses =  $this->testBonusService->getUserBonuses($carbonDate,$user);
        $advances = $this->salaryService->getUserAdvances($carbonDate, $user);
        $userSalary = Salary::where('user_id',22709)->first();
        $userWorkChart = WorkChartModel::find($user->working_day_id);
        $salaryAmount = $userSalary->amount ? $userSalary->amount : 70000;
        $workingHours = (int)$userWorkChart->time_end - (int)$userWorkChart->time_beg;
        $realWorkTime = ((int)$user->work_end - (int)$user->work_start);
        $toDay = Carbon::parse(now())->day;
        $hourlyPay = round($salaryAmount / $countDayWork / $workingHours, 2);

        foreach ($arMonthDay as $elem){
            if($elem['day_off'] != true && $elem['day'] <= $toDay){
                $salary[] = array_merge($elem,['salary' => number_format(round($realWorkTime * $hourlyPay * $currency_rate),0,'.','')],
                    ['bonuses' => number_format(round(collect($salaryBonuses)->sum('paid')),0,'.','')],
                    ['fines' => number_format(round($userTotalFines['total']),0,'.','')],
                    ['awards' => number_format(round(collect($obtainedBonuses)->sum('paid')),0,'.','')],
                    ['test_bonus' => number_format(round(collect($testBonuses)->sum('paid')),0,'.','')],
                    ['avanses' => number_format(round(collect($advances)->sum('paid')),0,'.','')],
                    ['time_start' => substr($user->work_start,0,5)]);
            }else{
                $salary[] = $elem;
            }
        }
        return $salary;
    }

    public function countDayWork($arMonthDay)
    {
        foreach ($arMonthDay as $elem){
            if($elem['day_off'] != true){
                $countDayWork[] = $elem['day_off'];
            }
        }
        return count($countDayWork);
    }

    public function countDayOff($arMonthDay)
    {
        foreach ($arMonthDay as $elem){
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
                    $dayItem = getDate(strtotime($i . strstr($date, '.')))['wday'];
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

    public function otherCharts($date,$startDay,$daysInMonth,$dayOff,$dayWork)
    {
        $week = $this->getWeek();
        $dayCount = 1;
        $dayCountOff = 1;
        $arMonthDay = [];
        for ($i = $startDay; $i <= $daysInMonth; $i++) {
                if ($dayCount > $dayWork) {
                    $arMonthDay[$i] = [
                        'day_name' => $week[getDate(strtotime($i . strstr($date, '.')))['wday']] ?? '',
                        'day' => $i,
                        'day_off' => true,
                    ];
                    if ($dayCountOff < $dayOff) {
                        for ($k = 1; $k < $dayOff; $k++) {
                            if ($i + $k <= $daysInMonth)
                                $arMonthDay[$i + $k] = [
                                    'day_name' => $week[getDate(strtotime($i + $k . strstr($date, '.')))['wday']] ?? '',
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
                            'day_name' => $week[getDate(strtotime($i . strstr($date, '.')))['wday']] ?? '',
                            'day' => $i,
                            'day_off' => false,
                        ];
                        $dayCount++;
                    }
        }
        return $arMonthDay;
    }
}