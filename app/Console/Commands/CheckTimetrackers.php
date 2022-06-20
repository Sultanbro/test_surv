<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Account;
use App\Timetracking;
use App\ProfileGroup;

class CheckTimetrackers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:timetrackers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set exit time to user if not pushed button';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('check timetrackers');
        
        $timetrackers = Timetracking::whereDate('enter', date('Y-m-d'))
            ->whereNull('exit')
            ->get();

        foreach ($timetrackers as $timetracker) {
          
            $groups = ProfileGroup::where('active', 1)->get();
            $user = User::find($timetracker->user_id);
            foreach ($groups as $key => $group) {
                if ($group->users != null) {
                    $users = json_decode($group->users);
                    if (in_array($user->id, $users)) {
                        $group_id = $group->id;
                    }
                }
                
            }
            if (isset($group_id)) {
                $user_group = ProfileGroup::find($group_id);
            }
            if (is_null($timetracker->exit)) {
                if($user && $user->work_end) {
                    $timetracker->exit = date('Y-m-d') . ' ' . $user->work_end;
                } else {
                    $timetracker->exit = date('Y-m-d') . ' ' . $user_group->work_end . ':00';
                }
            }
            // // $exit = strtotime($timetracker->exit);
            // // $enter = strtotime($timetracker->enter);
            // // $count_hours = $exit - $enter;
            // $timetracker->total_hours = $timetracker->total_hours + $count_hours;

            $timetracker->save();
        }
    }
}
