<?php

namespace App\Classes\Analytics;

use App\User;
use Auth;
use Carbon\Carbon;
use App\Timetracking;
use App\TimetrackingHistory;
use App\Models\Analytics\UserStat;

class DM
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа Детский мир
     */
    const ID = 31;
    const GROUP_ID = 31;
    const ACTIVITIES = [
        19, // Часы работы
        20, // колво действий
        21 // учет времени
    ];

    /**
     * Поля сводной таблицы
     */
    const S_TOTAL = 0; // первая пустая строка
    const S_IMPL = 1; // impl
    const S_PRCST = 2; // pr, cst
    const S_PRCSTLL = 3; // Pr, cstll
    const S_DIALOGS = 4; // Всего принято диалогов
    const S_HOURS = 5; // Факт часов
    const S_PLAN = 6; // Plan
    const S_DIALOGS_QUALITY = 7; // Качество диалогов
    const S_DIALOGS_AVG = 8; // Ср. Отработка диал-в на оператора
    const S_MANAGERS = 9; // Кол-во менеджеров
    const S_INCORRECT_DIALOGS = 10; // Кол-во некоррект диалогов


    public static function defaultSummaryTable()
    {
        return [
            ['rownumber' => 2, 'headers' => 0, 'pr' => 0, 'plan' => 0],
            ['rownumber' => 3, 'headers' => 'Impl', 'pr' => 0, 'plan' => 0],
            ['rownumber' => 4, 'headers' => 'Pr, cst',],
            ['rownumber' => 5, 'headers' => 'Pr, cstll', 'pr' => 0, 'plan' => 0],
            ['rownumber' => 6, 'headers' => 'Всего принято диалогов', 'pr' => 0, 'avg' => 0],
            ['rownumber' => 7, 'headers' => 'Факт часов', 'pr' => 0, 'avg' => 0],
            ['rownumber' => 8, 'headers' => 'Plan', 'pr' => 0, 'avg' => 0],
            ['rownumber' => 9, 'headers' => 'Качество диалогов', 'avg' => 0],
            ['rownumber' => 10, 'headers' => 'Ср. Отработка диал-в на оператора', 'avg' => 0],
            ['rownumber' => 11, 'headers' => 'Кол-во менеджеров', 'pr' => 0, 'avg' => 0],
            ['rownumber' => 12, 'headers' => 'Кол-во некоррект диалогов', 'pr' => 0, 'avg' => 0],
        ];
    }

    /**
     * Обновить часы работы и учет времени от количества действий
     * @param int $user_id
     * @param String $date 'Y-m-d'
     * @param String $day
     * @return void
     */
    public static function updateTimes(int $user_id, $date, $day)
    {

        $carbon = Carbon::parse($date)->day($day);

        $stat = UserStat::where([
            'date' => $carbon->format('Y-m-d'),
            'employee_id' => $user_id,
            'activity_id' => 20
        ])->first();

        if ($stat) {

            $actions = (int)$stat->value;

            $activity19 = UserStat::where([
                'date' => $carbon->format('Y-m-d'),
                'employee_id' => $user_id,
                'activity_id' => 19
            ])->first();

            $value_for_19 = self::getHoursByActionsForRussia($actions);

            if ($activity19) {
                $activity19->value = $value_for_19;
                $activity19->save();
            } else {
                UserStat::create([
                    'date' => $carbon->format('Y-m-d'),
                    'employee_id' => $user_id,
                    'activity_id' => 19,
                    'value' => $value_for_19
                ]);
            }

            $activity21 = UserStat::where([
                'date' => $carbon->format('Y-m-d'),
                'employee_id' => $user_id,
                'activity_id' => 21
            ])->first();

            $value_for_21 = self::getHoursByActions($actions);

            if ($activity21) {
                $activity21->data = $value_for_21;
                $activity21->save();
            } else {
                UserStat::create([
                    'date' => $carbon->format('Y-m-d'),
                    'employee_id' => $user_id,
                    'activity_id' => 21,
                    'value' => $value_for_21
                ]);
            }

            $carbon_date = Carbon::parse($date)->day($day);

            $tt = Timetracking::where('user_id', $user_id)
                ->whereYear('enter', $carbon_date->year)
                ->whereMonth('enter', $carbon_date->month)
                ->whereDay('enter', $day)
                ->orderBy('id', 'desc')->first();

            if ($tt) {
                $tt->total_hours = $value_for_21 * 60;
                $tt->updated = 1;
                $tt->save();
            } else {
                Timetracking::create([
                    'enter' => $carbon_date,
                    'user_id' => $user_id,
                    'updated' => 1,
                    'total_hours' => $value_for_21 * 60,
                ]);
            }

            TimetrackingHistory::create([
                'author_id' => Auth::user()->id,
                'author' => Auth::user()->name . ' ' . Auth::user()->last_name,
                'user_id' => $user_id,
                'description' => 'Изменено время с Аналитики на ' . $value_for_21,
                'date' => $carbon_date->format('Y-m-d')
            ]);
        }
    }


    /**
     * Alt for updateTimes with new UserStat
     */
    public static function updateTimesNew(int $user_id, $date)
    {
        // кол-во дейс.
        $setting = UserStat::query()->where(['date' => $date, 'user_id' => $user_id, 'activity_id' => 20])->first();

        if ($setting) {
            $actions = (float)$setting->value;
            // часы работы
            $activity19 = UserStat::query()->where(['date' => $date, 'user_id' => $user_id, 'activity_id' => 19])->first();

            $value_for_19 = self::getHoursByActionsForRussia($actions);

            if ($activity19) {
                $activity19->value = $value_for_19;
                $activity19->save();
            } else {
                UserStat::query()->create([
                    'date' => $date,
                    'user_id' => $user_id,
                    'activity_id' => 19,
                    'value' => $value_for_19
                ]);
            }
            // учеть премени
            $activity21 = UserStat::query()->where(['date' => $date, 'user_id' => $user_id, 'activity_id' => 21])->first();

            $value_for_21 = self::getHoursByActions($actions);
            if ($activity21) {
                $activity21->value = $value_for_21;
                $activity21->save();
            } else {
                UserStat::query()->create([
                    'date' => $date,
                    'user_id' => $user_id,
                    'activity_id' => 21,
                    'value' => $value_for_21
                ]);
            }

            $carbon_date = Carbon::parse($date);
            self::updateOrCreateTimeTrack($user_id, $value_for_21, $carbon_date);
        }
    }

    /**
     * Update timetracking by thitd activity called time accounting
     */
    public static function updateTimesByWorkHours($user_id, $date, $day, $value)
    {
        $carbon = Carbon::parse($date)->day($day);

        self::updateOrCreateTimeTrack($user_id, $value, $carbon);
    }

    /**
     * Определить часы
     */
    public static function getHoursByActions($actions)
    {
        return match ($actions) {
            $actions >= 17 && $actions <= 33 => 1,
            $actions >= 34 && $actions <= 50 => 2,
            $actions >= 51 && $actions <= 67 => 3,
            $actions >= 68 && $actions <= 84 => 4,
            $actions >= 85 && $actions <= 101 => 5,
            $actions >= 102 && $actions <= 118 => 6,
            $actions >= 119 && $actions <= 135 => 7,
            $actions >= 136 && $actions <= 152 => 8,
            $actions >= 153 => 9,
            default => 0,
        };
    }

    /**
     * Определить часы
     */
    public static function getHoursByActionsForRussia($actions)
    {
        switch ($actions) {
            case $actions >= 15 && $actions <= 29:
                $value = 1;
                break;
            case $actions >= 30 && $actions <= 44:
                $value = 2;
                break;
            case $actions >= 45 && $actions <= 59:
                $value = 3;
                break;
            case $actions >= 60 && $actions <= 74:
                $value = 4;
                break;
            case $actions >= 75 && $actions <= 89:
                $value = 5;
                break;
            case $actions >= 90 && $actions <= 104:
                $value = 6;
                break;
            case $actions >= 105 && $actions <= 119:
                $value = 7;
                break;
            case $actions >= 120 && $actions <= 134:
                $value = 8;
                break;
            case $actions >= 135:
                $value = 9;
                break;
            default:
                $value = 0;
        }

        return $value;
    }

    /**
     * Определить часы без переработки (без сверхурочных)
     */
    public static function getHoursByActionsWithoutOvertime($actions)
    {
        switch ($actions) {
            case $actions >= 1 && $actions <= 120:
                $div = $actions / 13;
                $value = $div - floor($div) >= 0.8 ? ceil($div) : floor($div);
                break;
            case $actions >= 121:
                $value = 9;
                break;
            default:
                $value = 0;
        }

        return $value;
    }

    /**
     * @param int $userId
     * @param int $value
     * @param Carbon $carbon
     * @return void
     */
    private static function updateOrCreateTimeTrack(int $userId, int $value, Carbon $carbon): void
    {
        $timeTrack = Timetracking::query()
            ->where('user_id', $userId)
            ->whereDate('enter', $carbon->format('Y-m-d'));

        if ($timeTrack->exists()) {
            $timeTrack->update([
                'total_hours' => $value * 60,
                'updated' => 1
            ]);
        } else {
            Timetracking::query()->create([
                'enter' => $carbon,
                'user_id' => $userId,
                'updated' => 1,
                'total_hours' => $value * 60,
            ]);
        }

        TimetrackingHistory::query()->create([
            'author_id' => auth()->id(),
            'author' => User::getUserById(auth()->id())->full_name,
            'user_id' => $userId,
            'description' => 'Изменено время с Аналитики на ' . $value,
            'date' => $carbon->format('Y-m-d')
        ]);

    }
}
