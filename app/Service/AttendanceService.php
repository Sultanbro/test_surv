<?php

namespace App\Service;

use App\Http\Requests\GetDayAttendanceRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * @param $request
     * @return void
     */
    public function storeAttendance($request): void
    {
        $users = $request->input('user_ids');

        foreach ($users as $user)
        {
            Attendance::query()->create([
                'user_id'    => $user,
                'manager_id' => $request->input('manager_id'),
                'group_id'   => $request->input('group_id'),
                'date'       => $request->input('date'),
                'comment'    => $request->input('comment') ?? null
            ]);
        }
    }

    /**
     * @param Attendance $attendance
     * @param $request
     * @return bool
     */
    public function updateAttendance(Attendance $attendance, $request): bool
    {
        return $attendance->update($request->all());
    }

    public function getAttendance(GetDayAttendanceRequest $request): array
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

        return $this->getFirstDayAttendance($managerId, $dates);
    }

    /**
     * @param $managerId
     * @param $dates
     * @return array
     */
    private function getFirstDayAttendance($managerId, $dates): array
    {
        $firstAttends = Attendance::query()
            ->select(DB::raw('MIN(date) as date'),'user_id','manager_id', 'group_id')
            ->where('manager_id','=', $managerId)
            ->whereIn('date', $dates)
            ->groupBy('user_id', 'manager_id', 'group_id')
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