<?php

namespace App;

use App\Repositories\Timetrack\TimetrackRepository;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\ProfileGroup;
use App\TimetrackingHistory;

class Timetracking extends Model
{
    const DEFAULT_WORK_START_TIME = '09:00';
    const DEFAULT_WORK_END_TIME   = '19:00';

    protected $table = 'timetracking';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'enter',
        'exit',
        'total_hours',
        'program_id',
        'updated',
    ];

    /**
     * updated 
     * 0 не редактировано
     * 1 редактировано
     * 2 аналитика изменила время. План выполнился и поставились фиксированные часы
     * 3 ucalls изменила время. Отработанные часы
     */

    protected $dates = [
        'enter',
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

    public static function getSumHoursPerDayByUsersIds($from_date, $to_date, $users_ids)
    {
        $hours = Timetracking::whereIn('user_id', $users_ids)
            ->whereBetween('enter', [$from_date, $to_date])
            ->get();

        $sum = 0;
        if($hours) {
            foreach ($hours as $hour) {
                if($hour->updated == 1) {
                    $sum += ($hour->total_hours / 60);   
                } else {
                    $sum += ($hour->total_hours / 60) > 9 ? 9 : $hour->total_hours / 60;   
                }
            }
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
        $users = User::withTrashed()->with([
            'timetracking' => function ($q) use ($request, $year) {
                $q->selectRaw("*,DATE_FORMAT(enter, '%e') as date, TIMESTAMPDIFF(minute, `enter`, `exit`) as minutes")->orderBy('id', 'ASC')
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
        ])->whereIn('id', array_unique($users_ids))->orderBy('last_name', 'asc')

            //->get(['id', 'email', DB::raw("CONCAT(name,' ',last_name) as full_name"), 'user_type', 'working_time_id']);

            ->select(['id', 'email', 'deleted_at', 'name', 'last_name', 'user_type', 'working_time_id', 'program_id', 'full_time', 'weekdays', 'timezone'])
            ->paginate($perPage);
    
        return $users;
    }


    public static function totalHours($date, $group_id) {

        $users = ProfileGroup::employees($group_id);
        
        $users = User::withTrashed()->whereIn('id', $users)->where('position_id', 32)->get(['id'])->toArray();
        $total_hours =  self::select(
                DB::raw('SUM(total_hours) as total_hours')
            ) 
            ->whereIn('user_id', $users)
            ->whereDate('enter', $date)
            ->first()
            ->total_hours;
           
        return $total_hours / 60;
    }

    public static function updateTimes($employee_id, $date, $total_hours) {
        $tt = self::where('user_id', $employee_id)
            ->whereDate('enter', $date)
            ->orderBy('id', 'desc')
            ->first();

        if($tt) {
            $tt->total_hours = (int)$total_hours;
            $tt->updated = 2;
            $tt->save();
        } else {
            Timetracking::create([
                'enter' => $date,
                'exit' => $date,
                'updated' => 2,
                'user_id' => $employee_id,
                'total_hours' => (int)$total_hours,
            ]);
        }

        TimetrackingHistory::create([
            'user_id' => $employee_id,
            'author_id' => Auth::user()->id,
            'author' => Auth::user()->last_name . ' ' . Auth::user()->name,
            'date' => $date,
            'description' => 'Изменено время с аналитики на ' .$total_hours . ' минут',
        ]);
    }
}
