<?php

namespace App\Service\Timetrack;

use App\DayType;
use App\Salary;
use App\Timetracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\Collection;

class TimetrackService
{
    /**
     * @param array $userFinesInformation
     * @param User $user
     * @param int $month
     * @param int $year
     * @param Carbon $date
     * @return array
     */
    public function getUserFinalSalary($salaryBonuses, $obtainedBonuses, $testBonuses, array $userFinesInformation, $avanses,  User $user, int $month, int $year, Carbon $date, $currency_rate): array{

        $data = [
            'salaries' => [],
            'times' => [],
            'hours' => [],
        ];

        $userFines = $userFinesInformation['fines'];
        $totalFines = $userFinesInformation['total'];


        $totalAvanses = $avanses->sum('paid');


        for($i = 1; $i <= $date->daysInMonth; $i++) {
            $m = $i;
            if(strlen($i) == 1) $m = '0'.$i;
            $data['salaries'][$i]['fines'] = isset($userFines[$m]) ? $userFines[$m]: [];
            $data['salaries'][$i]['bonuses'] = isset($salaryBonuses[$m]) ? $salaryBonuses[$m]: [];
            $data['salaries'][$i]['awards'] = isset($obtainedBonuses[$m]) ? $obtainedBonuses[$m]: [];
            $data['salaries'][$i]['test_bonus'] = isset($testBonuses[$m]) ? $testBonuses[$m]: [];
            $data['salaries'][$i]['avanses'] = isset($avanses[$m]) ? $avanses[$m]: [];

        }

        // Вычисление даты принятия
        $user_applied_at = $user->applied_at();

        $timetrackers = $this->getUserTimetracking($user, $year, $month, $user_applied_at, '>=');

        $trainee_days = DayType::selectRaw("DAY(date) as datex")
            ->where('user_id', $user->id)
            ->whereYear('date',  $date->year)
            ->whereMonth('date',  $date->month)
            ->whereIn('type', [5,6,7])
            ->get(['datex']);

        //  Рaбочие дни до принятия на работу
        $tts_before_apply =$this->getUserTimetracking($user, $year, $month, $user_applied_at, '<');



        for( $day = 1;$day <= $date->daysInMonth; $day++) {

            //count hourly pay
            $s = Salary::where('user_Id', $user->id)
                ->where('date', $date->day($day)->format('Y-m-d'))
                ->first();

            $zarplata = $s ? $s->amount : 70000;
            $working_hours = $user->workingTime ? $user->workingTime->time : 9;
            $ignore = $user->working_day_id == 1 ? [6,0] : [0];   // Какие дни не учитывать в месяце
            $workdays = workdays($date->year, $date->month, $ignore);

            $hourly_pay = $zarplata / $workdays / $working_hours;

            $hourly_pay = round($hourly_pay, 2);

            // count
            $data['times'][$day]['value'] = $timetrackers->where('date', $day)->count() > 0 ? $timetrackers->where('date', $day)->first()->enter->format('H:i') : '';
            $data['times'][$day]['fines'] = [];
            $data['times'][$day]['training'] = false;


            $hour = $timetrackers->where('date', $day)->count() > 0 ? $timetrackers->where('date', $day)->first()->total_hours / 60 : '';

            if ($hour < 0) {
                $hour = 0;
            }

            if($hour == '') {
                $hour = 0;
            }

            $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');

            $data['salaries'][$day]['training'] = false;

            if($trainee_days->where('datex', $day)->first()) {
                $hour = $user->working_time_id == 1 ? 8 : 9;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate * $user->internshipPayRate()), 0, '.', ''); // стажировочные на пол суммы
                // $data['salaries'][$day]['value'] = 0;
                $data['salaries'][$day]['training'] = true;
            } else if($tts_before_apply->where('date', $day)->first()) {
                $hour = $tts_before_apply->where('date', $day)->first()->total_hours / 60;
                $data['salaries'][$day]['value'] = number_format(round($hour * $hourly_pay * $currency_rate), 0, '.', '');
            }

            //if($tts_before_apply->where('date', $day)->first()) dd($tts_before_apply->where('date', $day)->first());

            $data['hours'][$day]['value'] = round($hour, 2);

            if($data['salaries'][$day]['training'] || $data['hours'][$day]['value'] == 0) $data['hours'][$day]['value'] = '';
            if($data['salaries'][$day]['value'] == 0) $data['salaries'][$day]['value'] = '';

            $data['salaries'][$day]['calculated'] =  round($hour, 2) . ' * ' . ($trainee_days->where('datex', $day)->first() ? $hourly_pay * $user->internshipPayRate() : $hourly_pay);

            $data['hours'][$day]['fines'] = [];
            $data['hours'][$day]['training'] = false;
        }

        return [
            'data' => [
                'salaries' => $data['salaries'],
                'times' => $data['times'],
                'hours' => $data['hours'],
            ],
            'totalFines' => $totalFines,
            'total_avanses' => $totalAvanses
        ];
    }

    public function getUserTimetracking(User $user, int $year, int $month, string $user_applied_at, string $operator ){
        return Timetracking::select([
            DB::raw('DAY(enter) as date'),
            DB::raw('sum(total_hours) as total_hours'),
            DB::raw('MIN(enter) as enter')
        ])
            ->whereMonth('enter', $month)
            ->whereYear('enter', date('Y'))
            ->whereDate('enter', $operator, Carbon::parse($user_applied_at)->format('Y-m-d'))
            ->where('user_id', $user->id)
            ->groupBy('date')
            ->get();
    }

}