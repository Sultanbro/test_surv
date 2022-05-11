<?php
namespace App\Classes\Analytics;

use Auth;
use App\User;
use Carbon\Carbon;
use App\Timetracking;
use App\ProfileGroup;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually as ASI;

class OperatorFact
{
    protected $group_id;
    public $date;
    public $day;

    public function __construct($group_id) {
        $this->group_id = $group_id;
        $this->date = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->day = 1;
    }

    /**
     * change date
     */
    public function setDate($date) {
        $this->day = Carbon::parse($date)->day;
        $this->date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        return $this;
    }

    
    
    /**
     * get operator fact total on month
     * $date Y-m-d startOfMonth
     */
    public function getTotal() {
        $total = 0;
        foreach($this->get() as $value) {
            $total += $value;
        }

        return $total;
    }

    /**
     * get operator fact to month
     * $date Y-m-d startOfMonth
     */
   
    public function get() {
        $arr = [];
        
        if($this->group_id == 42) return $this->getTotalsOfReports();
        
        if(in_array($this->group_id, [31,58,63])) return $this->getAnalyticsMinutes();

        
        $user_ids = json_decode(ProfileGroup::find($this->group_id)->users);
        $user_ids = User::withTrashed()->whereIn('ID', $user_ids)->where('position_id', 32)->get()->pluck('ID')->toArray();

        for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {

            $arr[$i] = 0;

            $ts_all = Timetracking::whereIn('user_id', $user_ids)
                ->whereDate('enter', Carbon::parse($this->date)->day($i)->format('Y-m-d'))
                ->whereNotNull('total_hours')
                ->where('program_id', 1)
                ->get()
                ->sum('total_hours');
       
            // $ts_all_p = Timetracking::whereIn('user_id', $user_ids)
            //     ->whereDate('enter', Carbon::parse($this->date)->day($i)->format('Y-m-d'))
            //     ->whereNull('program_id')
            //     ->get()
            //     ->sum('total_hours');

            $workhours = 9;
            if($this->group_id == 53) $workhours = 8;

            $arr[$i] = number_format(($ts_all) / 60 / $workhours, 1);
        }

        return $arr;
    }
    
    public function getTotalsOfReports() {
        $group = ProfileGroup::find(42);
        if (!empty($group) && $group->users != null) {
            $x_users = json_decode($group->users);
        }
        
        $users_ids = array_unique($x_users);
        
        $sum = Timetracking::getSumHoursPerMonthByUsersIds($users_ids, Carbon::parse($this->date)->month, Carbon::parse($this->date)->year);
        
        foreach ($sum as $key => $value) {
            $sum[$key] = number_format((float)$value / 9, 2, '.', '');
        }
        
        return $sum;
    }

    public function getAnalyticsMinutes() {
        $activity = 0;
        if($this->group_id == 31) $activity = 21;
        if($this->group_id == 58) $activity = 25;
        if($this->group_id == 63) $activity = 40;

        $asis = ASI::where([
            'date' => $this->date,
            'group_id' => $this->group_id,
            'type' => $activity,
        ])->get();

        $arr = [];
        for ($i = 1; $i <= Carbon::parse($this->date)->daysInMonth; $i++) {
            $arr[$i] = 0;
            foreach($asis as $asi) {
                $data = json_decode($asi->data, true);
                if(array_key_exists($i, $data)) {
                    $value = $data[$i] / 9;
                    $arr[$i] += $value;
                }
            }
        }

        return $arr;
    }

}   
