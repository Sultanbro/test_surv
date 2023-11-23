<?php

namespace App;

use App\Repositories\Timetrack\TimetrackRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * @mixin Builder
 */
class Timetracking extends Model
{
    const DEFAULT_WORK_START_TIME = '09:00';
    const DEFAULT_WORK_END_TIME   = '19:00';

    const DAY_STARTED = 1;
    const DAY_ENDED = 0;

    protected $table = 'timetracking';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'enter',
        'exit',
        'total_hours',
        'program_id',

        /**
         * updated
         * 0 не редактировано
         * 1 редактировано
         * 2 аналитика изменила время. План выполнился и поставились фиксированные часы
         * 3 ucalls изменила время. Отработанные часы
         */
        'updated',

        'status',
        'times',
    ];

    protected $dates = [
        'enter',
        'exit',
    ];

    protected $casts = [
        'times' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')->withTrashed();
    }

    public function scopeRunning($query)
    {
        $enter = app(TimetrackRepository::class);
        return $query->whereDate('enter', $enter->getWorkStartTime())->where('exit', null);
    }

    public function isStarted()
    {
        return $this->status == self::DAY_STARTED;
    }

    public function isEnded()
    {
        return $this->status == self::DAY_ENDED;
    }

    public function setStatus(int $value)
    {
        $this->status = $value;
        return $this;
    }

    public function setEnter(Carbon $value)
    {
        $this->enter = $value->setTimezone('UTC');
        return $this;
    }

    public function setExit(Carbon $value)
    {
        $this->exit = $value->setTimezone('UTC');
        return $this;
    }

    public function setTimes(array $value)
    {
        $this->times = $value;
        return $this;
    }

    public function addTime(Carbon $value, $tz = 'UTC')
    {
        $arr = $this->times;

        if(!$arr) {
            $arr = [];
            $arr[] = $this->enter->setTimezone($tz)->format('H:i');

            if($this->exit) {
                $arr[] = $this->exit->setTimezone($tz)->format('H:i');
            }
        }

        $arr[] = $value->setTimezone($tz)->format('H:i');

        return $this->setTimes($arr);
    }

    public function scopeWorkdayStarted($query)
    {
        return $query->where('status', self::DAY_STARTED);
    }

    public function isWorkEndTimeSetToNextDay(Carbon $worktimeEnd) : bool
    {
        return $worktimeEnd->hour < 9
            && Carbon::now($worktimeEnd->timezone)
                ->hour >= 9;
    }

    public static function getSumHoursPerDayByUsersIds($from_date, $to_date, $users_ids)
    {
        $hours = Timetracking::whereIn('user_id', $users_ids)
            ->whereBetween('enter', [$from_date, $to_date])
            ->get();

        if($hours->count() == 0) {
            return number_format(0, 2, '.', '');
        }

        $sum = 0;

        foreach ($hours as $hour) {

            $totalHours = $hour->total_hours / 60;

            if($hour->updated != 1 && $totalHours > 9) {
                $totalHours = 9;
            }

            $sum += $totalHours;
        }

        return number_format((float)$sum, 2, '.', '');
    }

    public static function getSumHoursPerMonthByUsersIds($users_ids, $month, $year)
    {
        $query_date = $year . '-' . $month . '-01';
        $first_day = date('Y-m-01', strtotime($query_date));
        $last_day = date('Y-m-t', strtotime($query_date));

        $summary = [];
        $day = $first_day;

        while (strtotime($day) <= strtotime($last_day)) {

            /*$from_date = date("Y-m-d", strtotime($day . ' - 1 days'));
            $to_date = date("Y-m-d", strtotime($day . '+ 1 days'));*/

            $from_date = date("Y-m-d 00:00:00", strtotime($day));
            $to_date = date("Y-m-d 23:59:59", strtotime($day));

            $summary[(int)date('d', strtotime($day))] = self::getSumHoursPerDayByUsersIds($from_date, $to_date, $users_ids);

            $day = date("Y-m-d", strtotime($day . '+ 1 days'));
        }

        return $summary;
    }

    public static function getTimeTrackingReport($request, $users_ids, $year)
    {

        $users = User::withTrashed()->with([
            'timetracking' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
                    ->whereMonth('enter', '=', $request->month)->whereYear('enter', $year);
            },

            'fines' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(day, '%e') as date")->whereMonth('day', '=', $request->month)->whereYear('day', $year);
            },
            'daytypes' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $request->month)->whereYear('date', $year);
            },
            'trackHistory' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $request->month)->whereYear('date', $year);
            }
        ])->whereIn('id', array_unique($users_ids))
        ->orderBy('last_name', 'asc')
            ->get(['id', 'email', DB::raw("CONCAT(last_name,' ',name) as full_name"), 'user_type', 'working_time_id', 'full_time']);

        return $users;
    }

    public static function getTimeTrackingReportPaginate($request, $users_ids, $year, $perPage = 1000)
    {
        return User::withTrashed()->with([
            'timetracking' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")
                    ->orderBy('id', 'ASC')
                    ->whereMonth('enter', '=', $request->month)
                    ->whereYear('enter', $year);
            },
            'fines' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(day, '%e') as date")->whereMonth('day', '=', $request->month)->whereYear('day', $year);
            },
            'daytypes' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $request->month)->whereYear('date', $year);
            },
            'trackHistory' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(date, '%e') as day")->whereMonth('date', '=', $request->month)->whereYear('date', $year);
            }
        ])->whereIn('id', array_unique($users_ids))
            ->orderBy('last_name', 'asc')

            //->get(['id', 'email', DB::raw("CONCAT(name,' ',last_name) as full_name"), 'user_type', 'working_time_id']);

            ->select(['id', 'email', 'deleted_at', 'name', 'last_name', 'user_type', 'working_time_id', 'program_id', 'full_time', 'weekdays', 'timezone'])
            ->paginate($perPage);
    }

    /**
     * @param string $date
     * @param int $group_id
     * @param ?array $positions
     * @return float|int
     */
    public static function totalHours(
        string $date,
        int $group_id,
        ?array $positions = []
    ): float|int
    {
        $users = \App\ProfileGroup::employees($group_id);

        $users = User::withTrashed()
            ->when(empty($positions), fn ($users) => $users->where('position_id', Position::OPERATOR_ID), fn ($users) => $users->whereIn('position_id', $positions))
            ->whereIn('id', $users)->get(['id'])->toArray();

        $total_hours =  self::select(
                DB::raw('SUM(total_hours) as total_hours')
            )
            ->whereIn('user_id', $users)
            ->whereDate('enter', $date)
            ->first()
            ->total_hours;

        return $total_hours / 60;
    }

    public static function updateTimes($employee_id, $date, $total_hours)
    {
        $auth = auth()->id();

        $timeTrack = Timetracking::query()
            ->where('user_id', $employee_id)
            ->whereDate('enter', $date);

        if ($timeTrack->exists())
        {
            $timeTrack?->update([
                'total_hours' => (int) $total_hours,
                'updated' => 2
            ]);
        } else {
            Timetracking::query()->create([
                'enter' => $date,
                'exit' => $date,
                'updated' => 2,
                'user_id' => $employee_id,
                'total_hours' => (int)$total_hours,
            ]);
        }

        \App\TimetrackingHistory::create([
            'user_id' => $employee_id,
            'author_id' => 5,
            'author' => User::getUserById($auth)->full_name,
            'date' => $date,
            'description' => 'Изменено время с аналитики на '.$total_hours.' минут',
        ]);
    }

    public static function getItemInWeek($userId){
        $firstDayOfWeek = now()->startOfWeek();
        $lastDayOfWeek = now()->endOfWeek();
        return self::where('user_id', $userId)
        ->whereBetween('enter', [$firstDayOfWeek, $lastDayOfWeek])
        ->get()
        ->map(function($item){
            return Carbon::parse($item['enter'])->toDateString();
        })
        ->unique()
        ->count();
    }
}
