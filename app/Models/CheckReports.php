<?php

namespace App\Models;

use App\ProfileGroup;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function filterCheckList($request)
    {
        $group = ProfileGroup::find($request->group_id);
        $check_users = [];

        foreach (json_decode($group->users) as $keys => $check_user){

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

            foreach ($allUserReports as $allUserReport){
                $check_users[$keys]['name'] = $getUset->name;
                $check_users[$keys]['last_name'] = $getUset->last_name;
                $check_users[$keys]['day'][$allUserReport['day']] = $allUserReport['count_check_auth'] .'/' .$allUserReport['count_check'];
                $check_users[$keys]['month'][$allUserReport['month']]  = $dayCountCheckAuth .'/' .$dayCountCheck;
                $check_users[$keys]['gr_id'] = $allUserReport['item_id'];
                $check_users[$keys]['total_day'] = $dayCountCheckAuth .'/' .$dayCountCheck;
                $check_users[$keys]['total_month'] = $monthCountCheckAuth.'/' .$monthCountCheck;
            }
        }
        return $check_users;
    }

}