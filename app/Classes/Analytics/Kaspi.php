<?php
namespace App\Classes\Analytics;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Trainee;
use App\Timetracking;
use App\TimetrackingHistory;
use App\DayType;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually as ASI;

class Kaspi 
{
    public $table1 = 'analytics_settings';
    public $table2 = 'analytics_settings_individually';

    /**
     * Группа Каспи
     */
    CONST ID = 42;
    CONST GROUP_ID = 42;

    /**
     * Место получить 
     */
    public static function getPlace($date, $type) {
        $as = AnalyticsSettings::where([
            'date' => $date,
            'group_id' => self::ID,
        ])->first();

        if($type == 'pros') {
            $index = 14; // Рейтинг 1-5
        } else {
            $index = 17; // Напоминиания кредит
        }
        
        $value = 0;
        if($as) {
            if($as->data && array_key_exists($index, $as->data) && array_key_exists('pr', $as->data[$index])) {
                $value = $as->data[$index]['pr'];
            }
        }

        return number_format((float)$value, 1);
    }

}
