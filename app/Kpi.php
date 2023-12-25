<?php

namespace App;

use App\Models\Analytics\Activity;
use App\Models\Analytics\ActivityPlan;
use App\Models\Analytics\IndividualKpi;
use App\Models\Analytics\IndividualKpiIndicator;
use App\Models\Analytics\KpiIndicator;
use App\Models\Analytics\UserStat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'kpi';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'group_id',
        'kpi_80_99',
        'kpi_100',
        'nijn_porok',
        'verh_porok',
    ];

    /**
     * $date Y-m-d
     * return @int
     */
    public static function userKpi(int $user_id, string $date = '', $renew = 0)
    {

        if ($renew == 0) { // if not require renew, return saved value

            $skpi = SavedKpi::where('user_id', $user_id);
            if ($date != '') {
                $skpi->where('date', Carbon::parse($date)->day(1)->format('Y-m-d'));
            } else {
                $skpi->where('date', Carbon::now()->day(1)->format('Y-m-d'));
            }
            $skpi = $skpi->first();

            $res = 0;
            if ($skpi) {
                $res = $skpi->total;
            }
            return $res;
        }


        $kpi_total = 0;

        $user = User::withTrashed()->find($user_id);

        // check has individaul kpi


        $ind_kpi = IndividualKpi::where('user_id', $user_id)->first();
        if ($ind_kpi) {
            return self::indKpi($user_id, $date);
        }

        // count by common kpi
        $groups = [];
        if ($user) {
            $groups = $user->inGroups();
        }

        foreach ($groups as $group) {
            $kpi_total += self::groupKpi($user_id, $group->id, $date);

        }


        $sk = SavedKpi::where('user_id', $user_id)
            ->where('date', $date)
            ->update([
                'total' => intval($kpi_total)
            ]);


        return intval($kpi_total);
    }

    private static function indKpi(int $user_id, string $date = '')
    {

        $kpi_configs = IndividualKpi::where('user_id', $user_id)->first();
        $nijn_porok = $kpi_configs->nijn_porok;
        $verh_porok = $kpi_configs->verh_porok;
        $kpi_80_99 = $kpi_configs->kpi_80_99;
        $kpi_100 = $kpi_configs->kpi_100;
        $user = User::withTrashed()->find($user_id);
        $time_rate = $user->full_time == 1 ? 1 : 0.5;
        $kpi_80_99 = $kpi_80_99 * $time_rate;
        $kpi_100 = $kpi_100 * $time_rate;
        $kpi_indicators = IndividualKpiIndicator::where('user_id', $user_id)->where('activity_id', '!=', 0)->get();
        $ind_kpi = 0;

        foreach ($kpi_indicators as $kpi_indicator) {

            $activity = Activity::withTrashed()->find($kpi_indicator->activity_id);

            if ($kpi_indicator->group_id == 0) {
                $kpi_indicator->completed = 0;
            } else if (in_array($kpi_indicator->group_id, [48])) {

                $kpi_indicator->completed = 0;
                if ($activity->type == 'conversion') { // Conversion in TableSummaryRecruiting
                    $as = \App\AnalyticsSettings::where('group_id', $kpi_indicator->group_id)->where('date', $date)->where('type', 'basic')->first();
                    if ($as && array_key_exists(9, $as->data) && array_key_exists(0, $as->data) && array_key_exists('fact', $as->data[0]) && array_key_exists('fact', $as->data[9])) {
                        $kpi_indicator->completed = round($as->data[9]['fact'] / $as->data[0]['fact'] * 100, 2);
                    }
                }

            } else {
                $kpi_indicator->completed = 0;
                if ($activity) {
                    $kpi_indicator->completed = UserStat::getTotalActivityProgress($user_id, $activity, $date);
                }
            }

            if ($kpi_indicator->completed > 100) {
                $kpi_indicator->completed = 100;
            }

            if ($activity && $activity->plan_unit == 'less_sum') {
                $nijn_porok = 0;
                $kpi_80_99 = $kpi_100;
            }

            $ind_kpi += self::sumOfActivity($nijn_porok, $verh_porok, $kpi_indicator->completed, $kpi_indicator->ud_ves, $kpi_80_99, $kpi_100);
        }

        return (int)$ind_kpi;
    }

    private static function groupKpi(int $user_id, int $group_id, string $date = '')
    {
        $nijn_porok = 80;
        $verh_porok = 100;
        $kpi_80_99 = 0;
        $kpi_100 = 0;

        $isset_kpi = self::issetKpi($group_id, $date);
        if ($isset_kpi) {
            $kpi_configs = $isset_kpi;

            $nijn_porok = $kpi_configs->nijn_porok;
            $verh_porok = $kpi_configs->verh_porok;
            $kpi_80_99 = $kpi_configs->kpi_80_99;
            $kpi_100 = $kpi_configs->kpi_100;
        }

        $user = User::withTrashed()->find($user_id);
        $time_rate = $user->full_time == 1 ? 1 : 0.5;
        $kpi_80_99 = $kpi_80_99 * $time_rate;
        $kpi_100 = $kpi_100 * $time_rate;
        $indicators = KpiIndicator::where('group_id', $group_id)->get()->pluck('activity_id')->toArray();
        $activity_ids = array_unique($indicators);
        $activities = Activity::withTrashed()->whereIn('id', $activity_ids)->get();

        $group_kpi = 0;

        foreach ($activities as $activity) {

            if ($date != '') {
                $activity_plan = ActivityPlan::where([
                    'activity_id' => $activity->id,
                    'month' => self::explodeDate($date, 'month'),
                    'year' => self::explodeDate($date, 'year'),
                ])->first();

                if ($activity_plan) {
                    $activity->daily_plan = $activity_plan->plan;
                    $activity->ud_ves = $activity_plan->ud_ves;
                    $activity->plan_unit = $activity_plan->plan_unit;
                    $activity->unit = $activity_plan->plan_unit;
                }
            }

            $completed = UserStat::getActivityProgress($user_id, $group_id, $activity, $date);

            if ($completed > 100) {
                $completed = 100;
            }
            $activity->completed = $completed;

            $activity->sum_prem = $kpi_100 * $activity->ud_ves / 100;

            if ($activity->plan_unit == 'less_sum') {
                $nijn_porok = 0;
                $kpi_80_99 = $kpi_100;
            }


            $group_kpi += self::sumOfActivity($nijn_porok, $verh_porok, $activity->completed, $activity->ud_ves, $kpi_80_99, $kpi_100);
        }


        return $group_kpi;
    }

    /**
     * Возвращает Kpi за текущий месяц с KpiChange, при отсутствии ищет в Kpi
     * @return Object
     */
    private static function issetKpi(int $group_id, string $date)
    {
        $isset_kpi = Kpi::where('group_id', $group_id)->first();
        if ($date != '') {
            if ($isset_kpi) {
                $_isset_kpi_for_this_month = KpiChange::where([
                    'kpi_id' => $isset_kpi->id,
                    'month' => self::explodeDate($date, 'month'),
                    'year' => self::explodeDate($date, 'year'),
                ])->first();
                if ($_isset_kpi_for_this_month) {
                    $isset_kpi->nijn_porok = $_isset_kpi_for_this_month->nijn_porok;
                    $isset_kpi->verh_porok = $_isset_kpi_for_this_month->verh_porok;
                    $isset_kpi->ud_ves = $_isset_kpi_for_this_month->ud_ves;
                    $isset_kpi->kpi_80_99 = $_isset_kpi_for_this_month->kpi_80_99;
                    $isset_kpi->kpi_100 = $_isset_kpi_for_this_month->kpi_100;
                }
            }
        }

        return $isset_kpi;
    }

    private static function sumOfActivity($nijn_porok, $verh_porok, $completed, $ud_ves, $kpi_80_99, $kpi_100)
    {
        $result = 0;

        $nijn_porok = $nijn_porok / 100;
        $verh_porok = $verh_porok / 100;
        $completed = $completed / 100;
        $ud_ves = $ud_ves / 100;

        if ($completed > $nijn_porok) {
            if ($completed < $verh_porok) {
                $result = $kpi_80_99 * $ud_ves * ($completed - $nijn_porok) * $verh_porok / ($verh_porok - $nijn_porok);

            } else {
                $result = $kpi_100 * $ud_ves * $completed;
            }
        } else {
            $result = 0;
        }


        if ($result < 0) {
            $result = 0;
        }
        return $result;
    }

    /**
     * date Y-m-d
     * @return String
     */
    private static function explodeDate(string $date, string $type = 'day')
    {
        if ($date == '') $date = date('Y-m-d');
        $date = explode("-", $date);
        if ($type == 'day') $result = $date[2];
        if ($type == 'month') $result = $date[1];
        if ($type == 'year') $result = $date[0];
        return $result;
    }
}
