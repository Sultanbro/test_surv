<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use Carbon\Carbon;
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
     */
    public function handle(): void
    {
        $data = [];
        $date = $this->argument('date');

        $userIds = DB::table('group_user')
            ->where([
                ['group_id', '=', ProfileGroup::IT_DEPARTMENT_ID],
                ['status', '=', 'active']
            ])
            ->whereNotIn('user_id', [5, 24937, 25473])
            ->get()->pluck('user_id')->toArray();

        $enterTime = isset($date) ? Carbon::parse($date)->setTime('02', '30', '00') : Carbon::createFromTime('02', '30', '00');

        foreach ($userIds as $userId)
        {
            $data[] = [
                'user_id'       => $userId,
                'total_hours'   => 0,
                'updated'       => 0,
                'program_id'    => null,
                'enter'         => $enterTime->format('Y-m-d H:i:s'),
                'created_at'    => now(),
                'updated_at'    => now()
            ];
        }

        DB::table('timetracking')->insert($data);
    }
}
