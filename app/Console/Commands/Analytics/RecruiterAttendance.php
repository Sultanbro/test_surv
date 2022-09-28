<?php

namespace App\Console\Commands\Analytics;

use App\Classes\Analytics\Recruiting;
use App\ProfileGroup;
use App\Service\AttendanceService;
use App\Service\RecruitingActivityService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecruiterAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruiter:attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill UserStat for recruiters in BP';

    /**
     * date Y-m-d
     */
    protected $date;


    /**
     * helper service for recruiting
     */
    protected $activity;

    /**
     * helper service for recruiting
     */
    protected $attendance_service;

    

    /**
     * users in recruiting
     */
    protected $users;

    /**
     * Create a new command instance.
     *
     * @return void 
     */
    public function __construct()
    {
        parent::__construct();

        $this->date = date('Y-m-d');

        $this->activity = new RecruitingActivityService();
        $this->activity->setDate($this->date);
        
        $this->attendance_service = new AttendanceService();

        // TODO users
        // get users
        $group = ProfileGroup::find(Recruiting::GROUP_ID);
        $this->users = json_decode($group->users);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('start');
       

        foreach($this->users as $user_id) {

            $this->activity->setUser($user_id);

            $was_on_first_day    = $this->traineesWasOnFirstDay($user_id);
            $was_from_second_day = $this->traineesWasFromSecondDay($user_id);

            $this->activity->save(Recruiting::I_FIRST_DAY_TRAINED,       $was_on_first_day);
            $this->activity->save(Recruiting::I_SECOND_DAY_TRAINED_FROM, $was_from_second_day);
            
        }

        $this->line('end');
    }

    /**
     * @param $user_id
     * @return int
     */
    public function traineesWasOnFirstDay($user_id) : int
    {
        return $this->attendance_service->getFirstDayAttendance($user_id, $this->date);
    }

    /**
     * @param $user_id
     * @return int
     */
    public function traineesWasFromSecondDay($user_id) : int
    {
        return $this->attendance_service->getSecondDayAttendance($user_id, $this->date);
    }

}
