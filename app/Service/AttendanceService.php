<?php

namespace App\Service;

use App\DayType;
use App\Http\Requests\GetDayAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Bitrix\Lead;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * @param $request
     * @return void
     */
    public function storeAttendance($request): bool
    {
        $userId = $request->input('user_id');
        $user   = User::query()->findOrFail($userId);

        DayType::query()->create([
            'user_id'    => $userId,
            'type'       => $request->input('type'),
            'admin_id'   => auth()->id() ?? 5263,
            'date'       => $request->input('date'),
            'email'      => $user->email ?? null
        ]);

        return true;
    }

    /**
     * @param DayType $attendance
     * @param $request
     * @return bool
     */
    public function updateAttendance(DayType $attendance, UpdateAttendanceRequest $request): bool
    {
        $request = $request->only('date');

        return $attendance->update([
            'date' => $request['date'],
        ]);
    }

    /**
     * DEPRECATED
     */
    public function getAttendanceForMonth(GetDayAttendanceRequest $request): array
    {
        $managerId = $request->input('manager_id');
        $month     = $request->input('month');
        $year      = $request->input('year');

        $daysInMonth = $this->getDate($month, $year)->daysInMonth;

        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++)
        {
            $date = Carbon::parse($year . '-' . $month . '-' . $i)->toDateString();
            $dates[] = $date;
        }

        return $this->getFirstDayAttendanceForMonth($managerId, $dates);
    }

    /**
     * @param $managerId
     * @param $date
     * @return int
     */
    public function getFirstDayAttendance($managerId, String $date): int
    {
        $user = User::withTrashed()->find($managerId);

        if(!$user) {
            return 0;
        }

        return Lead::with('daytypes')
            ->whereHas('daytypes', function ($query) use ($date) {
                $query->whereDate('date', $date)
                    ->whereIn('type', [
                        DayType::DAY_TYPES['TRAINEE'],
                        DayType::DAY_TYPES['RETURNED']
                    ]);
            })
            ->whereDate('invite_at', $date)
            ->where('resp_id', $user->email)
            ->get()
            ->count();
    }

    /**
     * @param $managerId
     * @param $date
     * @return int
     */
    public function getSecondDayAttendance($managerId, String $date): int
    {
        $user = User::withTrashed()->find($managerId);

        if(!$user) {
            return 0;
        }

        return Lead::with('daytypes')
            ->whereHas('daytypes', function ($query) use ($date) {
                $query->whereDate('date', '>', $date)
                    ->whereIn('type', [
                        DayType::DAY_TYPES['TRAINEE'],
                        DayType::DAY_TYPES['RETURNED']
                    ]);
            })
            ->whereDate('invite_at', $date)
            ->where('resp_id', $user->email)
            ->get()
            ->count();
    }

     /**
     * @param $managerId
     * @param $date
     * @return int
     */
    public function getCurrentAttendance($managerId, String $date): int
    {
        $user = User::withTrashed()->find($managerId);

        if(!$user) {
            return 0;
        }

        return Lead::with('daytypes')
            ->whereHas('daytypes', function ($query) use ($date) {
                $query->whereDate('date', $date)
                    ->whereIn('type', [
                        DayType::DAY_TYPES['TRAINEE'],
                        DayType::DAY_TYPES['RETURNED']
                    ]);
            })
            ->where('resp_id', $user->email)
            ->get()
            ->count();
    }

    /**
     * @param $managerId
     * @param $dates
     * @return array
     */
    private function getFirstDayAttendanceForMonth($managerId, $dates): array
    {
        $firstAttends = DayType::query()
            ->select(DB::raw('MIN(date) as date'),'user_id','admin_id')
            ->where('admin_id','=', $managerId)
            ->whereIn('type', [DayType::DAY_TYPES['TRAINEE'], DayType::DAY_TYPES['RETURNED']])
            ->whereIn('date', $dates)
            ->whereIn('user_id', $user_ids)
            ->groupBy('user_id', 'admin_id')
            ->orderBy('date','ASC')
            ->get()->toArray();

        return collect($firstAttends)->groupBy('date')->map(function ($attend) {
            return $attend->count();
        })->toArray();
    }

    /**
     * @param $month
     * @param $year
     * @param $day
     * @return Carbon
     */
    private function getDate($month, $year, $day = null)
    {
        return Carbon::parse($year . '-' . $month );
    }
}