<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StartDayForItDepartmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start_day:it_department {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматический нажать на кнопку НАЧАТЬ ДЕНЬ для IT отдела';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $data = [];
        $date = $this->argument('date');

        /**
         * Получаем всех кроме IT специалистов.
         */
        $userIds = DB::table('group_user')
            ->select(['user_id'])
            ->join('profile_groups', 'profile_groups.id', '=', 'group_user.group_id')
            ->where('profile_groups.name', '=', ProfileGroup::IT_DEPARTMENT_NAME)
            ->where('group_user.status', '=', 'active')
            ->get()
            ->toArray();

        /**
         * Получаем время прихода.
         * Если есть аргумент получаем время выхода.
         */
        $enterTime = isset($date) ? Carbon::parse($date)->setTime('02', '30', '00') : Carbon::createFromTime('02', '30', '00');
        $exitTime = isset($date) ? Carbon::parse($date)->setTime('12', '00', '00')->format('Y-m-d H:i:s') : null;

        /**
         * За выходные дни не можем поставить отметку.
         */
        if ($enterTime->isSaturday() || $enterTime->isSunday()) {
            return;
        }

        $timeTrack = DB::table('timetracking')
            ->whereIn('user_id', $userIds)
            ->whereDate('enter', $enterTime->format('Y-m-d'))
            ->get();

        foreach ($userIds as $userId) {
            $exist = $timeTrack->where('user_id', $userId)->count() > 0;

            if (!$exist) {
                $data[] = [
                    'user_id' => $userId,
                    'total_hours' => isset($date) ? 480 : 0,
                    'updated' => 0,
                    'program_id' => null,
                    'enter' => $enterTime->format('Y-m-d H:i:s'),
                    'exit' => $exitTime,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        DB::table('timetracking')->insert($data);
    }
}
