<?php

namespace App\Models;

use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CheckReports extends Model
{
    use HasFactory;


    protected $table = 'check_reports';

    public $timestamps = true;



    protected $fillable = [
        'check_users_id',
        'check_id',
        'count_check',
        'count_check_auth',
        'date',
        'item_type',
        'item_id',
    ];


    public function get_average_value($month,$year,$check_user_id,$type,$type_id){


//        $month = date('n'); ;
//        $year = 2022;

        $getDay =  cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $start = new \DateTime("01.$month.$year");
        $end = new \DateTime("$getDay.$month.$year 23:59");


        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end);

        $weekNumber = 1;
        $weeks = array();


        $average_value = [];









        foreach ($dateRange as $date) {

            $weeks[$weekNumber]['date'][] = $date->format('j-n-Y');
            $weeks[$weekNumber]['sss'][] = $date->format('w');

            if ($date->format('w') == 0) {
                $weeks[$weekNumber]['count'] = count($weeks[$weekNumber]['date']);
                $weekNumber++;
            }else{
                $weeks[$weekNumber]['count'] = count($weeks[$weekNumber]['date']);
            }


        };











        foreach ($weeks as $md => $week){

            for ($i = 0;$i < $week['count'];$i++){

                if (isset($week['count'])){
                    $countWeek = $week['count'] - 1;
                }else{
                    $countWeek = count($week['date']);
                }


                if ($i == 0){
                    $week_middl[$md]['start'] = explode('-',$week['date'][$i]);
                    $week_middl[$md]['end'] = explode('-',$week['date'][$countWeek]);
                }





                $week_middl[$md]['count_check'] = CheckReports::on()->where('check_users_id',$check_user_id)
                    ->where('year',$week_middl[$md]['start'][2])->where('month',$week_middl[$md]['start'][1])
                    ->where('day','>=',$week_middl[$md]['start'][0])
                    ->where('day','<=',$week_middl[$md]['end'][0])
                    ->where('item_id',$type_id)
                    ->where('item_type',$type)->sum('count_check');

                $week_middl[$md]['count_check_auth'] = CheckReports::on()->where('check_users_id',$check_user_id)
                    ->where('year',$week_middl[$md]['start'][2])->where('month',$week_middl[$md]['start'][1])
                    ->where('day','>=',$week_middl[$md]['start'][0])
                    ->where('day','<=',$week_middl[$md]['end'][0])
                    ->where('item_id',$type_id)
                    ->where('item_type',$type)->sum('count_check_auth');


                $average_value[$md] = $week_middl[$md]['count_check'] -  $week_middl[$md]['count_check_auth'];

//                $average_value[$md] =  10 -  3;

            }

        }




//        return $week_middl;
        return $average_value;

    }



    public function getMonth($check_user,$year,$group_id){



        $allUserReports['all'] = CheckReports::on()->where('check_users_id',$check_user)
            ->where('year',$year)
            ->where('item_id',$group_id)
            ->get()->toArray();









        for ($i = 1;$i < 13; $i++){



            foreach ($allUserReports['all'] as $allUserReport){


                if (isset($allUserReport['month']) && $i == $allUserReport['month']){
                    $getIntegers['count_check'][$i][] = $allUserReport['count_check'];
                    $getIntegers['count_check_auth'][$i][] = $allUserReport['count_check_auth'];
                }

            }


        }





        $all_monthe['count_check'] = [];
        $all_monthe['count_check_auth'] = [];

        $month=[];


        if (isset($getIntegers)){

            foreach ($getIntegers['count_check'] as $key => $getInteger_c){

                if (isset($getInteger_c)){
                    $num = 0;
                    foreach ($getInteger_c as $getInteger){
                        $num = $num + $getInteger;
                        $all_monthe['count_check'][$key] = $num;
                    }
                }
            }

            foreach ($getIntegers['count_check_auth'] as $key => $getInteger_a){

                if (isset($getInteger_a)){
                    $num = 0;
                    foreach ($getInteger_a as $getInteger){
                        $num = $num + $getInteger;
                        $all_monthe['count_check_auth'][$key] = $num;
                    }
                }
            }

            for ($i = 1; $i <= 12;$i++){
                if (isset($all_monthe['count_check'][$i])){
                    $month[$i] = $all_monthe['count_check_auth'][$i] ."/". $all_monthe['count_check'][$i];
                }
            }



        }

        return $month;
    }

    public function filterCheckList($request)
    {
        $check_users = null;
        $check_users = [];


//        return  ['check_users'=>$check_users,'individual_type'=>2,'individual_current'=>$request->group_id,$request->individual_type];;
//

        if ($request->individual_type == 2 || $request->individual_type == null){
            $group = ProfileGroup::find($request->group_id);
            $user_ids = $group->users == null ? [] : json_decode($group->users);

            foreach ($user_ids as $keys => $check_user){


                $getUset = User::find($check_user);

                $allUserReports = CheckReports::on()->where('check_users_id',$check_user)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->group_id)
                    ->get()->toArray();

                $dayCountCheck = CheckReports::on()->where('check_users_id',$check_user)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->group_id)
                    ->sum('count_check');
                $dayCountCheckAuth = CheckReports::on()->where('check_users_id',$check_user)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->group_id)
                    ->sum('count_check_auth');

                $monthCountCheck = CheckReports::on()->where('check_users_id',$check_user)
                    ->where('year',$request->year)->where('item_id',$request->group_id)->sum('count_check');

                $monthCountCheckAuth = CheckReports::on()->where('check_users_id',$check_user)
                    ->where('year',$request->year)->where('item_id',$request->group_id)->sum('count_check_auth');


                $this->getMonth($check_user,$request->year,$request->group_id);

                if (!empty($allUserReports)){
                    foreach ($allUserReports as $allUserReport){
                        $check_users[$keys]['name'] = $getUset->name;
                        $check_users[$keys]['last_name'] = $getUset->last_name;
                        $check_users[$keys]['day'][$allUserReport['day']] = $allUserReport['count_check_auth'] .'/' .$allUserReport['count_check'];
                        $check_users[$keys]['month']  = $this->getMonth($check_user,$request->year,$request->group_id);
                        $check_users[$keys]['gr_id'] = $allUserReport['item_id'];
                        $check_users[$keys]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                        $check_users[$keys]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                        $check_users[$keys]['allUserReports'] = $allUserReports;
                        $check_users[$keys]['average'] = $this->get_average_value($request->month,$request->year,$check_user,$request->individual_type,$request->group_id);
                    }
                }else{

                    $allUserReports = CheckReports::on()->where('check_users_id',$check_user)
                        ->where('year',$request->year)
                        ->where('item_id',$request->group_id)
                        ->get()->toArray();

                    foreach ($allUserReports as $allUserReport){
                        $check_users[$keys]['name'] = $getUset->name;
                        $check_users[$keys]['last_name'] = $getUset->last_name;
                        $check_users[$keys]['day'][$allUserReport['day']] = 0 .'/' . 0;
                        $check_users[$keys]['month']  = $this->getMonth($check_user,$request->year,$request->group_id);
                        $check_users[$keys]['gr_id'] = $allUserReport['item_id'];
                        $check_users[$keys]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                        $check_users[$keys]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                        $check_users[$keys]['allUserReports'] = $allUserReports;
                        $check_users[$keys]['average'] = $this->get_average_value($request->month,$request->year,$check_user,$request->individual_type,$request->group_id);
                    }


                }




            }
            return ['check_users'=>$check_users,'individual_type'=>2,'individual_current'=>$request->group_id,$request->individual_type];
        }
        elseif ($request->individual_type == 1) {

            $getUser = User::find($request->individual_type_id);

            $allUserReports = CheckReports::on()->where('check_users_id',$getUser->id)
                ->where('year',$request->year)->where('month',$request->month)
                ->where('item_id',$getUser->id)
                ->get()->toArray();

            $dayCountCheck = CheckReports::on()->where('check_users_id',$getUser->id)
                ->where('year',$request->year)->where('month',$request->month)
                ->where('item_id',$getUser->id)
                ->sum('count_check');
            $dayCountCheckAuth = CheckReports::on()->where('check_users_id',$getUser->id)
                ->where('year',$request->year)->where('month',$request->month)
                ->where('item_id',$getUser->id)
                ->sum('count_check_auth');

            $monthCountCheck = CheckReports::on()->where('check_users_id',$getUser->id)
                ->where('year',$request->year)->where('item_id',$getUser->id)->sum('count_check');

            $monthCountCheckAuth = CheckReports::on()->where('check_users_id',$getUser->id)
                ->where('year',$request->year)->where('item_id',$getUser->id)->sum('count_check_auth');




            if (!empty($allUserReports)){
                foreach ($allUserReports as $allUserReport){
                    $check_users[0]['name'] = $getUser->name;
                    $check_users[0]['last_name'] = $getUser->last_name;
                    $check_users[0]['day'][$allUserReport['day']] = $allUserReport['count_check_auth'] .'/' .$allUserReport['count_check'];
                    $check_users[0]['month']  = $this->getMonth($getUser->id,$request->year,$getUser->id);
                    $check_users[0]['gr_id'] = $allUserReport['item_id'];
                    $check_users[0]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                    $check_users[0]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                    $check_users[0]['average'] = $this->get_average_value($request->month,$request->year,$request->individual_type_id,$request->individual_type,$request->individual_type_id);
                }
            }else{

                $allUserReports = CheckReports::on()->where('check_users_id',$getUser->id)
                    ->where('year',$request->year)
                    ->where('item_id',$getUser->id)
                    ->get()->toArray();

                foreach ($allUserReports as $allUserReport){

                    $check_users[0]['name'] = $getUser->name;
                    $check_users[0]['last_name'] = $getUser->last_name;
                    $check_users[0]['day'][$allUserReport['day']] = 0 .'/' . 0;
                    $check_users[0]['month']  = $this->getMonth($getUser->id,$request->year,$getUser->id);
                    $check_users[0]['gr_id'] = $allUserReport['item_id'];
                    $check_users[0]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                    $check_users[0]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                    $check_users[0]['average'] = $this->get_average_value($request->month,$request->year,$request->individual_type_id,$request->individual_type,$request->individual_type_id);
                }
            }



            return ['check_users'=>$check_users,'individual_type'=>1,'individual_current'=>$getUser->id];

        }
        elseif ($request->individual_type == 3){

            $users = User::where('position_id',$request->individual_type_id)->select('id','last_name','name')->get();

            foreach ($users as $keys => $check_user){
                $allUserReports = CheckReports::on()->where('check_users_id',$check_user->id)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->individual_type_id)
                    ->get()->toArray();

                $dayCountCheck = CheckReports::on()->where('check_users_id',$check_user->id)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->individual_type_id)
                    ->sum('count_check');

                $dayCountCheckAuth = CheckReports::on()->where('check_users_id',$check_user->id)
                    ->where('year',$request->year)->where('month',$request->month)
                    ->where('item_id',$request->individual_type_id)
                    ->sum('count_check_auth');

                $monthCountCheck = CheckReports::on()->where('check_users_id',$check_user->id)
                    ->where('year',$request->year)->where('item_id',$request->individual_type_id)->sum('count_check');

                $monthCountCheckAuth = CheckReports::on()->where('check_users_id',$check_user->id)
                    ->where('year',$request->year)->where('item_id',$request->individual_type_id)->sum('count_check_auth');


                if (!empty($allUserReports)){
                    foreach ($allUserReports as $allUserReport){
                        $check_users[$keys]['name'] = $check_user->name;
                        $check_users[$keys]['last_name'] = $check_user->last_name;
                        $check_users[$keys]['day'][$allUserReport['day']] = $allUserReport['count_check_auth'] .'/' .$allUserReport['count_check'];
                        $check_users[$keys]['month']  = $this->getMonth($check_user->id,$request->year,$request->group_id);
                        $check_users[$keys]['gr_id'] = $allUserReport['item_id'];
                        $check_users[$keys]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                        $check_users[$keys]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                        $check_users[$keys]['week_start'] = $monthCountCheck;
                        $check_users[$keys]['week_end'] = $monthCountCheckAuth;
                        $check_users[$keys]['average'] = $this->get_average_value($request->month,$request->year,$check_user->id,$request->individual_type,$request->individual_type_id);
                    }
                }else{
                    $allUserReports = CheckReports::on()->where('check_users_id',$check_user->id)
                        ->where('year',$request->year)
                        ->where('item_id',$request->individual_type_id)
                        ->get()->toArray();

                    foreach ($allUserReports as $allUserReport){
                        $check_users[$keys]['name'] = $check_user->name;
                        $check_users[$keys]['last_name'] = $check_user->last_name;
                        $check_users[$keys]['day'][$allUserReport['day']] = $allUserReport['count_check_auth'] .'/' .$allUserReport['count_check'];
                        $check_users[$keys]['month']  = $this->getMonth($check_user->id,$request->year,$request->group_id);
                        $check_users[$keys]['gr_id'] = $allUserReport['item_id'];
                        $check_users[$keys]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                        $check_users[$keys]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
                        $check_users[$keys]['week_start'] = $monthCountCheck;
                        $check_users[$keys]['week_end'] = $monthCountCheckAuth;
                        $check_users[$keys]['average'] = $this->get_average_value($request->month,$request->year,$check_user->id,$request->individual_type,$request->individual_type_id);
                    }

                }



            }
            return ['check_users'=>$check_users,'individual_type'=>3,'individual_current'=>$request->individual_type_id];
        }


    }

    public function test($month,$year,$check_user_id,$type,$type_id){


//        $month = date('n'); ;
//        $year = 2022;

        $getDay =  cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $start = new \DateTime('01.7.2022');
        $end = new \DateTime("$getDay.7.2022 23:59");
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end);

        $weekNumber = 1;
        $weeks = array();


        $average_value = [];

        foreach ($dateRange as $date) {
            $weeks[$weekNumber]['date'][] = $date->format('j-n-Y');
            if ($date->format('w') == 0) {
                $weeks[$weekNumber]['count'] = count($weeks[$weekNumber]['date']);
                $weekNumber++;
            }
        }




        foreach ($weeks as $md => $week){

            for ($i = 0;$i < $week['count'];$i++){
                $countWeek = $week['count'] - 1;

                if ($i == 0){
                    $week_middl[$md]['start'] = explode('-',$week['date'][$i]);
                    $week_middl[$md]['end'] = explode('-',$week['date'][$countWeek]);
                }



                $week_middl[$md]['count_check'] = CheckReports::on()->where('check_users_id',$check_user_id)
                    ->where('year',$week_middl[$md]['start'][2])->where('month',$week_middl[$md]['start'][1])
                    ->where('day','>=',$week_middl[$md]['start'][0])
                    ->where('day','<=',$week_middl[$md]['end'][0])
                    ->where('item_id',$type_id)
                    ->where('item_type',$type)->sum('count_check');

                $week_middl[$md]['count_check_auth'] = CheckReports::on()->where('check_users_id',$check_user_id)
                    ->where('year',$week_middl[$md]['start'][2])->where('month',$week_middl[$md]['start'][1])
                    ->where('day','>=',$week_middl[$md]['start'][0])
                    ->where('day','<=',$week_middl[$md]['end'][0])
                    ->where('item_id',$type_id)
                    ->where('item_type',$type)->sum('count_check_auth');


                $average_value[$md] = $week_middl[$md]['count_check'] -  $week_middl[$md]['count_check_auth'];

            }
        }



        return $average_value;

    }

}