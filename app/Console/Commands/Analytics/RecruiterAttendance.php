<?php

namespace App\Console\Commands\Analytics;

use App\Classes\Analytics\Recruiting;
use App\ProfileGroup;
use App\Service\AttendanceService;
use App\Service\RecruitingActivityService;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecruiterAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recruiter:attendance {date?}';

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

        $this->activity = new RecruitingActivityService();
     
        
        $this->attendance_service = new AttendanceService();

        // TODO users
        // get users
//        $group = ProfileGroup::find(Recruiting::GROUP_ID);
//        $users = json_decode($group->users);
//
//        $this->users = User::withTrashed()
//            ->with('user_description')
//            ->whereHas('user_description', function ($query) {
//                $query->where('is_trainee', 0);
//            })
//            ->whereIn('id', $users)
//            ->get(['id'])
//            ->pluck('id')
//            ->toArray();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('start Users remained: ' . count($this->users));
        
        /**
         * create dates array
         */
        if($this->argument('date')) {
            $dates = [
                $this->argument('date')
            ];
        } else {
            $dates = [
                Carbon::now()->format('Y-m-d'),
                Carbon::now()->subDays(1)->format('Y-m-d'),
                Carbon::now()->subDays(2)->format('Y-m-d'),
                Carbon::now()->subDays(3)->format('Y-m-d'),
                Carbon::now()->subDays(4)->format('Y-m-d'),
                Carbon::now()->subDays(5)->format('Y-m-d'),
                Carbon::now()->subDays(6)->format('Y-m-d'),
            ];
        }

        foreach ($dates as $key => $date) {
            $this->activity->setDate($date);

            foreach($this->users as $user_id) {
                
                $this->activity->setUser($user_id);
    
                $was_on_first_day    = $this->traineesWasOnFirstDay($user_id, $date);
                $was_from_second_day = $this->traineesWasFromSecondDay($user_id, $date);
    
                dump($user_id. '    ' . $was_on_first_day . '  ' . $was_from_second_day . ' ' . $date);
                $this->activity->save(Recruiting::I_FIRST_DAY_TRAINED,       $was_on_first_day);
                $this->activity->save(Recruiting::I_SECOND_DAY_TRAINED_FROM, $was_from_second_day);
                
            }
        }

        $this->line('end');
    }

    /**
     * @param $user_id
     * @return int
     */
    public function traineesWasOnFirstDay($user_id, $date) : int
    {
        return $this->attendance_service->getFirstDayAttendance($user_id, $date);
    }

    /**
     * @param $user_id
     * @return int
     */
    public function traineesWasFromSecondDay($user_id, $date) : int
    {
        return $this->attendance_service->getCurrentAttendance(
            $user_id,
            $date,
        );
    }

}
