<?php

namespace App;

use App\Repositories\Timetrack\TimetrackRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int $user_id
 * @property string $enter
 * @property string $exit
 * @property string $total_hours
 * @property string $program_id
 * @property string $updated
 * @property string $status
 * @property string $times
 * @property User $user
 * @mixin Builder
 */
class Timetracking extends Model
{
    const DEFAULT_WORK_START_TIME = '09:00';
    const DEFAULT_WORK_END_TIME = '19:00';
    const DEFAULT_WORK_CHARTS_TYPE = 0;

    const DAY_STARTED = 1;
    const DAY_ENDED = 0;
    public $timestamps = true;
    protected $table = 'timetracking';
    protected $fillable = [
        'user_id',
        'enter',
        'exit',
        'total_hours',
        'program_id',
        'updated',
        'status',
        'times',
        /**
         * updated
         * 0 не редактировано
         * 1 редактировано
         * 2 аналитика изменила время. План выполнился и поставились фиксированные часы
         * 3 ucalls изменила время. Отработанные часы
         */
    ];

    protected $dates = [
        'enter',
        'exit',
    ];

    protected $casts = [
        'times' => 'array',
    ];

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

    public static function getSumHoursPerDayByUsersIds($from_date, $to_date, $users_ids)
    {
        $hours = Timetracking::whereIn('user_id', $users_ids)
            ->whereBetween('enter', [$from_date, $to_date])
            ->get();

        if ($hours->count() == 0) {
            return number_format(0, 2, '.', '');
        }

        $sum = 0;

        foreach ($hours as $hour) {

            $totalHours = $hour->total_hours / 60;

            if ($hour->updated != 1 && $totalHours > 9) {
                $totalHours = 9;
            }

            $sum += $totalHours;
        }

        return number_format((float)$sum, 2, '.', '');
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
        return User::withTrashed()
            ->select([
                'id',
                'email',
                'deleted_at',
                'name',
                'last_name',
                'user_type',
                'working_time_id',
                'program_id',
                'full_time',
                'weekdays',
                'timezone',
                DB::raw("cast((SELECT SUM(total_hours / 60) FROM timetracking WHERE users.id = timetracking.user_id AND MONTH(enter) = $request->month AND YEAR(enter) = $request->year) as decimal(10,2)) AS total_hours")
            ])
            ->with([
                'group_users',
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
            ])
            ->whereIn('id', array_unique($users_ids))
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
        int    $group_id,
        ?array $positions = []
    ): float|int
    {
        $positions = empty($positions) ? [Position::operator()->id] : $positions;
        $users = ProfileGroup::employeesNew($group_id, $date, $positions);

        $total_hours = self::query()
            ->whereIn('user_id', $users->pluck('id')->toArray())
            ->whereDate('enter', $date)
            ->sum('total_hours');

        return $total_hours / 60;
    }

    public static function updateTimes($employee_id, $date, $total_hours): void
    {
        $auth = auth()->id();
        $record = Timetracking::query()
            ->where('user_id', $employee_id)
            ->whereDate('enter', $date)
            ->first();

        if ($record) {
            $record->update([
                'total_hours' => $total_hours,
                'updated' => 2,
            ]);
        } else {
            $record = Timetracking::query()->create([
                'user_id' => $employee_id,
                'enter' => $date,
                'total_hours' => $total_hours,
                'updated' => 2,
                'status' => Timetracking::DAY_STARTED
            ]);
        }

        User::setExit($record, $date);

        TimetrackingHistory::query()->create([
            'user_id' => $employee_id,
            'author_id' => $auth,
            'author' => User::getUserById($auth)->full_name,
            'date' => $date,
            'description' => 'Изменено время с аналитики на ' . $total_hours . ' минут',
        ]);
    }

    public static function getItemInWeek($userId)
    {
        $firstDayOfWeek = now()->startOfWeek();
        $lastDayOfWeek = now()->endOfWeek();
        return self::where('user_id', $userId)
            ->whereBetween('enter', [$firstDayOfWeek, $lastDayOfWeek])
            ->get()
            ->map(function ($item) {
                return Carbon::parse($item['enter'])->toDateString();
            })
            ->unique()
            ->count();
    }

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

    public function addTime(Carbon $value, $tz = 'UTC'): static
    {
        $arr = $this->times;
        $enter = Carbon::parse($this->enter, $tz)->format('H:i');
        $exit = Carbon::parse($this->exit, $tz)->format('H:i');
        if (!$arr) {
            $arr = [];
            $arr[] = $enter;

            if ($this->exit) {
                $arr[] = $exit;
            }
        }

        $arr[] = $value->setTimezone($tz)->format('H:i');
        $this->setTimes($arr);

        return $this;
    }

    public function setTimes(array $value): static
    {
        $this->times = $value;
        $this->save();
        return $this;
    }

    public function scopeWorkdayStarted($query)
    {
        return $query->where('status', self::DAY_STARTED);
    }

    public function isWorkEndTimeSetToNextDay(Carbon $worktimeEnd): bool
    {
        return $worktimeEnd->hour < 9
            && Carbon::now($worktimeEnd->timezone)
                ->hour >= 9;
    }
}
