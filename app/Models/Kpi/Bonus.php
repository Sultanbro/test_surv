<?php

namespace App\Models\Kpi;

use App\Models\Admin\ObtainedBonus;
use App\Models\Scopes\ActiveScope;
use App\Traits\ActivateAbleModelTrait;
use App\Traits\Filterable;
use App\Traits\TargetJoin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;
use Carbon\Carbon;
use App\ProfileGroup;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\RecruiterStat;
use App\Models\Kpi\Traits\Expandable;
use App\Models\Kpi\Traits\Targetable;
use App\Models\Kpi\Traits\WithActivityFields;
use App\Models\Kpi\Traits\WithCreatorAndUpdater;
use App\Service\Department\UserService;
use DB;
use Illuminate\Support\Arr;

class Bonus extends Model
{
    use SoftDeletes, HasFactory, ActivateAbleModelTrait;
    use WithCreatorAndUpdater, WithActivityFields;
    use Targetable, TargetJoin, Expandable;
    use Filterable;

    protected $table = 'kpi_bonuses';

    public $timestamps = true;

    protected $appends = ['target', 'group_id', 'source', 'expanded'];

    protected $casts = [
        'created_at' => 'date:d.m.Y H:i',
        'updated_at' => 'date:d.m.Y H:i',
    ];

    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'title',
        'sum',
        'group_id',
        'activity_id',
        'unit',
        'quantity',
        'daypart',
        'from',
        'to',
        'text',
        'created_by',
        'updated_by',
        'is_active'
    ];

    /**
     * Unit
     */
    const FOR_ONE = 'one';
    const FOR_ALL = 'all';
    const FOR_FIRST = 'first';
    const PERCENT = 'percent';

    /**
     * Dayparts
     */
    const FULL_DAY = 0;
    const PERIOD = 1;
    const MONTH = 2;

    /**
     * Получает все активные кв-премий без доп запросов.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ActiveScope);
    }

    public function obtainedBonuses(): HasMany
    {
        return $this->hasMany('App\Models\Admin\ObtainedBonus', 'bonus_id');
    }

    /**
     * count obtained bonuses of users in group
     */
    public static function obtained_in_group($group_id, $date)
    {
        $bonuses = self::query()
            ->where('targetable_id', $group_id)
            ->where('targetable_type', 'App\ProfileGroup')
            ->get();

        // get users
        $users = (new UserService)->getUsersAll($group_id, $date)->pluck('id')->toArray();

//        dd_if(
//            $group_id,
//            $users
//        );
        //  fill $awards array
        foreach ($users as $user_id) {
            $awards[$user_id] = 0;
            $comments[$user_id] = '';
        }

        $awards = []; // bonuses
        $comments = []; // bonuses

        foreach ($bonuses as $bonus) {

            // если награда 0 и активность не указана пропускаем
            if ($bonus->sum == 0) continue;
            if ($bonus->activity_id == 0) continue;
            // за первый
            if ($bonus->unit == self::FOR_FIRST) {

                $best_user = 0;
                $best_value = 0;

                // Если группа Евраз и Хоум  подтягивать с каллибро
                if (in_array($group_id, [53, 57])) {

                    $best_user = self::fetch_best_user_from_callibro($bonus, $group_id, $date);

                    // save award to the best user
                    if ($best_user != 0) {

                        $data = [
                            'user_id' => $best_user,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => $bonus->sum,
                            'comment' => $bonus->title . ' : ' . $best_value . ';'
                        ];

                        ObtainedBonus::createOrUpdate($data, $bonus->daypart);
                    }

                } else {

                    foreach ($users as $user_id) {

                        // Если группа Рекрутинг
                        // @TODO должна быть только у BPartners
                        if ($group_id == 48) {
                            $val = self::fetch_value_from_activity_for_recruting($bonus, $user_id, $date);
                        } else {
                            $val = self::fetch_value_from_activity_new($bonus, $user_id, $date);
                        }

                        // лучший результат
                        if ((int)$val >= $bonus->quantity && (int)$val >= $best_value) {
                            $best_user = $user_id;
                            $best_value = (int)$val;
                        }
                    }

                    // nullify awards if they are not actual
                    ObtainedBonus::query()
                        ->where('bonus_id', $bonus->id)
                        ->where('date', $date)
                        ->delete();

                    // save award to the best user
                    if ($best_user != 0 && (int)$best_value >= $bonus->quantity) {
                        $data = [
                            'user_id' => $best_user,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => $bonus->sum,
                            'comment' => $bonus->title . ' : ' . $best_value . ';'
                        ];

                        ObtainedBonus::createOrUpdate($data, $bonus->daypart);
                    }
                }

            }

            // за все
            if ($bonus->unit == self::FOR_ALL) {

                // nullify awards if they are not actual
                ObtainedBonus::where('bonus_id', $bonus->id)
                    ->where('date', $date)
                    ->delete();

                foreach ($users as $user_id) {

                    dump('*              ' . $user_id);

                    // Если группа Рекрутинг
                    // @TODO должна быть только у BPartners
                    if ($group_id == 48) {
                        $val = self::fetch_value_from_activity_for_recruting($bonus, $user_id, $date);
                    } else {
                        $val = self::fetch_value_from_activity_new($bonus, $user_id, $date);
                    }

                    dump('HH  ' . $val . ' --- ' . $bonus->quantity);

                    // план выполнен
                    if ((int)$val >= $bonus->quantity) {

                        $data = [
                            'user_id' => $user_id,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => $bonus->sum,
                            'comment' => $bonus->title . ' : ' . (int)$val . ';'
                        ];

                        ObtainedBonus::createOrUpdate($data, $bonus->daypart);
                    }
                }

            }

            // за каждую единиу
            if ($bonus->unit == self::FOR_ONE) {

                foreach ($users as $user_id) {

                    if ($group_id == 48) { // рекрутинг @TODO должна быть только у BPartners

                        $val = self::fetch_value_from_activity_for_recruting($bonus, $user_id, $date);

                    } else if ( // Если группа Евраз и Хоум  подтягивать с каллибро
                        in_array($group_id, [53, 57, 79])
                        && in_array($bonus->activity_id, [16, 37, 18, 38, 146, 147])
                    ) { // Минуты и согласия

                        $val = self::fetch_value_from_callibro($bonus, $group_id, $date, $user_id);

                    } else {

                        $val = self::fetch_value_from_activity_new($bonus, $user_id, $date);

                    }

                    $data = [
                        'user_id' => $user_id,
                        'date' => $date,
                        'bonus_id' => $bonus->id,
                        'amount' => (int)$val * $bonus->sum,
                        'comment' => $bonus->title . ' : ' . $val . ';'
                    ];

                    ObtainedBonus::createOrUpdate($data, $bonus->daypart);
                }
            }

            // проценты от продаж
            if ($bonus->unit == self::PERCENT) {

                // nullify awards if they are not actual
                ObtainedBonus::where('bonus_id', $bonus->id)
                    ->where('date', $date)
                    ->delete();

                foreach ($users as $user_id) {

                    dump('*              ' . $user_id);

                    // Если группа Рекрутинг
                    // @TODO должна быть только у BPartners
                    if ($group_id == 48) {
                        $val = self::fetch_value_from_activity_for_recruting($bonus, $user_id, $date);
                    } else {
                        $val = self::fetch_value_from_activity_new($bonus, $user_id, $date);
                    }

                    dump('HH  ' . $val . ' --- ' . $bonus->quantity);

                    // план выполнен
                    if ((int)$val > 0) {

                        $data = [
                            'user_id' => $user_id,
                            'date' => $date,
                            'bonus_id' => $bonus->id,
                            'amount' => ($val * $bonus->sum) / 100,
                            'comment' => $bonus->title . ' : ' . (int)$val . ';'
                        ];

                        ObtainedBonus::createOrUpdate($data, $bonus->daypart);
                    }
                }

            }

        }

        return $awards;
    }


    /**
     * Fetch value number from callibro
     */
    public static function fetch_value_from_callibro($bonus, $group_id, $date, $user_id)
    {
        $vars = self::prepare_callibro_vars($bonus, $group_id, $date);

        // FIND USER
        $user = User::withTrashed()->find($user_id);
        if (!$user) return 0;

        $account = DB::connection('callibro')->table('call_account')
            ->where('email', $user->email)
            ->first();

        if (!$account) return 0;
        $vars['account_id'] = $account->id;

        // get calls
        $items = self::callibro_query($vars);

        return $items->count();
    }

    /**
     * Prepare vars for calls table in callibro
     */
    public static function prepare_callibro_vars($bonus, $group_id, $date)
    {
        $vars = [];

        if ($bonus->daypart == 1) {
            $vars['start_date'] = $date . ' ' . $bonus->from . ':00';
            $vars['end_date'] = $date . ' ' . $bonus->to . ':00';
        } elseif ($bonus->daypart == 2) {
            $date = Carbon::parse($date);

            $vars['start_date'] = $date->startOfMonth()->format('Y-m-d') . ' 00:00:00';
            $vars['end_date'] = $date->endOfMonth()->format('Y-m-d') . ' 23:59:59';
        } else {
            $vars['start_date'] = $date . ' 00:00:00';
            $vars['end_date'] = $date . ' 23:59:59';
        }

        if ($group_id == 53) {
            $vars['dialer_id'] = 398;
            $vars['script_status_ids'] = [2519]; // Cтатус в скрипте: Дата Визита
        }

        if ($group_id == 79) {
            $vars['dialer_id'] = 444;
            $vars['script_status_ids'] = [13559];
        }

        $vars['type'] = 'calls';
        if (in_array($bonus->activity_id, [18, 38, 146])) {
            $vars['type'] = 'aggrees';
        }

        return $vars;
    }

    /**
     *  query to calls table
     */
    public static function callibro_query($vars)
    {
        if ($vars['type'] == 'calls') {
            $items = DB::connection('callibro')->table('calls')
                ->select('call_account_id as account_id', 'billsec', 'start_time')
                ->whereBetween('start_time', [$vars['start_date'], $vars['end_date']])
                ->where('billsec', '>=', 10)
                ->where('call_dialer_id', $vars['dialer_id'])
                ->where('cause', '!=', 'SYSTEM_SHUTDOWN');
        } else { // aggrees
            $items = DB::connection('callibro')->table('calls')
                ->select('call_account_id as account_id', 'billsec', 'start_time')
                ->whereBetween('start_time', [$vars['start_date'], $vars['end_date']])
                ->where('correct_or_not', '!=', 2)
                ->where('call_dialer_id', $vars['dialer_id'])
                ->whereIn('script_status_id', $vars['script_status_ids']);
        }

        if (array_key_exists('account_id', $vars)) {
            $items = $items->where('call_account_id', $vars['account_id']);
        }

        return $items->get();
    }

    /**
     * String $type 'calls' or 'aggrees'
     * Bonus $bonus
     * int $group_id
     * String $date Y-m-d
     */
    public static function fetch_best_user_from_callibro($bonus, $group_id, $date)
    {
        $vars = self::prepare_callibro_vars($bonus, $group_id, $date);

        $items = self::callibro_query($vars);

        // get leader
        $callibro_account_id = self::getLeader($items, $bonus->quantity);


        // find leadr email
        $leader = DB::connection('callibro')->table('call_account')
            ->find($callibro_account_id);

        // find user id in lara
        $leader_id = 0;
        if ($leader) {
            $user = User::withTrashed()->where('email', $leader->email)->first();
            if ($user) {
                $leader_id = $user->id;
            }
        }

        return $leader_id;
    }

    /**
     * Determine who first reached the quantity from collection of calls
     */
    public static function getLeader($calls, $quantity)
    {

        $accounts = [];
        $last_calls = [];

        foreach ($calls as $call) {
            if (!array_key_exists($call->account_id, $accounts)) $accounts[$call->account_id] = 0;
            $accounts[$call->account_id]++;
            if ($accounts[$call->account_id] <= $quantity) {
                $last_calls[$call->account_id] = $call->start_time;
            }
        }

        $filteredAccounts = array_filter($accounts, function ($value) use ($quantity) {
            return ($value >= $quantity);
        });

        $keys = array_keys($filteredAccounts);

        $filteredLastCalls = [];
        foreach ($last_calls as $account_id => $last_call) {
            if (in_array($account_id, $keys)) {
                $filteredLastCalls[$account_id] = $last_call;
            }
        }

        $first = 0;
        $first_time = 0;

        if (count($filteredAccounts) > 0) {
            foreach ($filteredLastCalls as $account_id => $time) {
                $time = Carbon::parse($time)->timestamp;
                if ($first_time == 0 || $time < $first_time) {
                    $first_time = $time;
                    $first = $account_id;
                }
            }
        }

        return $first;
    }

    /**
     * Fetch value from
     */
    public static function fetch_value_from_activity($activity_id, $user_id, $date)
    {
        if ($activity_id == 0) return 0;

        $date = Carbon::parse($date);

        $stat = UserStat::where([
            'date' => $date->format('Y-m-d'),
            'user_id' => $user_id,
            'activity_id' => $activity_id
        ])->first();

        return $stat ? $stat->value : 0;
    }

    /**
     * Fetch value from UserStat
     */
    public static function fetch_value_from_activity_new($bonus, $user_id, $date)
    {
        if ($bonus->activity_id == 0) return 0;

        $stat = UserStat::query()
            ->select([
                DB::raw('SUM(value) as sum'),
            ])
            ->where('user_id', $user_id)
            ->where('activity_id', $bonus->activity_id);

        if ($bonus->daypart == 2) {

            $date = Carbon::parse($date);

            $stat->whereMonth('date', $date->month)
                ->whereYear('date', $date->year);


        } else {
            $stat->where('date', $date);
        }

        return $stat->first()->sum;
    }

    /**
     * Fetch value from
     *
     * @return int|float
     */
    public static function fetch_value_from_activity_for_recruting($bonus, $user_id, $date)
    {
        $indexes = [
            22 => 0,
            45 => 1,
            49 => 2,
            50 => 3,
            51 => 4,
            52 => 5,
            53 => 6,
            54 => 7,
        ]; // activity => index

        if (!array_key_exists($bonus->activity_id, $indexes)) {
            return 0;
        }

        $date = Carbon::parse($date);

        if ($bonus->activity_id == 45) {

            $records = RecruiterStat::query()
                ->where('calls', '>', 0)
                ->where('user_id', $user_id);

            if ($bonus->daypart != 2) {
                $records->where('date', $date);
            } else {
                $records->whereMonth($date->month)
                    ->whereYear($date->year);
            }

            if ($bonus->daypart == 1) {
                $records->where('hour', '>=', $bonus->from)
                    ->where('hour', '<', $bonus->to);
            }

            return $records->get()->sum('calls');
        }

        $stat = UserStat::where([
            'date' => $date->format('Y-m-d'),
            'user_id' => $user_id,
            'activity_id' => $bonus->activity_id
        ])->first();

        return $stat ? $stat->value : 0;
    }

    public static function getPotentialBonusesHtml($group_id)
    {
        $group = ProfileGroup::find($group_id);

        $bonuses = self::where('group_id', $group_id)->get();

        $html = '<b>';
        $html .= $group ? $group->name : 'Отдел';
        $html .= '</b><br>';

        //me($bonuses);
        if ($bonuses->count() > 0) {
            foreach ($bonuses as $key => $bonus) {
                $html .= `<div class="kaspi__item">
                    <img src="/images/dist/kaspi-gift-green.png" alt="" class="kaspi__item-img">
                    <div class="kaspi__item-about">
                        <div class="kaspi__item-title">
                            {$bonus->sum} KZT
                        </div>
                        <div class="kaspi__item-text">{$bonus->text}</div>
                    </div>
                </div>`;
            }
        } else {
            $html .= 'К сожалению, пока по данному проекту не предусмотрены бонусы<br>';
        }

        return $html;

    }

    public static function getEurasBestUser($from, $to)
    {
        $group = ProfileGroup::find(79);
        $users = json_decode($group->users);
        $group_users = User::whereIn('id', $users)->get();
        $awards = [];
        foreach ($group_users as $user) {
            $account = DB::connection('callibro')->table('call_account')->where('email', $user->email)->first();
            if ($account) {
                $call = DB::connection('callibro')->table('calls')
                    ->where('call_dialer_id', 444)
                    ->where('call_account_id', $account->id)
                    ->where('script_status_id', 13559)
                    ->whereBetween('start_time', [$from, $to])
                    ->orderBy('id', 'asc')
                    ->take(15)
                    ->get();
            }

            if (sizeof($call) == 15) {
                if (sizeof($awards) == 0) {
                    $awards[] = [$user->id, sizeof($call), $call[0]->start_time];
                } else {
                    if (Carbon::parse($awards[0][2])->gt(Carbon::parse($call[0]->start_time))) {
                        $awards[0] = [$user->id, sizeof($call), $call[0]->start_time];
                    }
                }
            }
        }
        //'2022-07-13 09:00:00' to '2022-07-13 13:00:00' - bonus 14628 Билостоцкая Наталья
        return $awards ? $awards[0][0] : 0;
    }
}
