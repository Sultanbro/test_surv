<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckUsers;
use App\Models\CheckList;
use App\Models\CheckReports;
use App\ProfileGroup;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Artisan;

class CheckListController extends Controller
{
    public function store(Request $request)
    {
       if ($request['countView'] < 11 && $request['countView'] != 0){




           if (isset($request['valueGroups']) && !empty($request['valueGroups'])){

               foreach ($request['valueGroups'] as $keys => $group){
                   $validateGR = CheckList::on()->where('item_id',$group['code'])->get()->toArray();
                   if (!empty($validateGR)){
                       return response(['success'=>false]);
                   }
               }

               if (empty($validateGR)){
                   foreach ($request['valueGroups'] as $keys => $group){
                       $profileGroups = ProfileGroup::on()->find($group['code']);
                       $checkList = new CheckList();
                       $checkList['title'] = $profileGroups->name;
                       $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                       $checkList['auth_name'] = auth()->user()->NAME;
                       $checkList['auth_last_name'] = auth()->user()->LAST_NAME;
                       $checkList['active_check_text'] = json_encode($request['arrCheckInput']);
                       $checkList['count_view'] = $request['countView'];
                       $checkList['item_type'] = 1;
                       $checkList['item_id'] = $profileGroups->id;
                       $checkList->save();
                       $this->saveGroup($profileGroups,$checkList,$request);
                   }
                   return response(['success'=>true]);
               }


           }
       }
    }

    public function saveGroup($profileGroups,$checkList,$request)
    {
        if (!empty($profileGroups['users'])){
            foreach (json_decode($profileGroups['users']) as $profile_users_id){
                $dataBaseUser = User::on()->find($profile_users_id)->toArray();
                $check_users = new CheckUsers();
                $check_users['name'] = $dataBaseUser['NAME'];
                $check_users['last_name'] = $dataBaseUser['LAST_NAME'];
                $check_users['check_list_id'] = $checkList->id;
                $check_users['check_users_id'] = $dataBaseUser['ID'];
                $check_users['check_reports_id'] = $this->saveReports($checkList,$dataBaseUser,$request,$profileGroups);
                $check_users['count_view'] = $request['countView'];
                $check_users['item_type'] = 1;
                $check_users['item_id'] = $profileGroups['id'];
                $check_users->save();


//                if (!empty($profileGroups['users'])){
//
//
//
//                    foreach (json_decode($profileGroups['users']) as $profile_users_id){
//
//
//
//                    }
//
//                }



            }
        }
    }

    public function saveReports($checkList= null,$dataBaseUser=null,$request=null,$profileGroups=null)
    {

        $check_reports_save = new CheckReports();
        $check_reports_save['check_id'] = $checkList->id;
        $check_reports_save['check_users_id'] = $dataBaseUser['ID'];
        $check_reports_save['year'] = date('Y');
        $check_reports_save['month'] = date('n');
        $check_reports_save['day'] = date('d');
        $check_reports_save['count_check'] = count($request['arrCheckInput']);
        $check_reports_save['count_check_auth'] = 0;
        $check_reports_save['item_type'] = 1;
        $check_reports_save['item_id'] = $profileGroups['id'];
        $check_reports_save->save();

        return $check_reports_save->id;
    }

    public function listViewCheck()
    {
        $checkList = CheckList::on()->get()->toArray();

       return $checkList;
    }

    public function deleteCheck(Request$request){



        CheckList::on()->find($request['delete_id'])->delete();

    }

    public function editCheck(Request$request)
    {


        $check_array = CheckList::on()->find($request['check_id'])->toArray();

        return $check_array;
    }

    public function editSaveCheck(Request$request){

        if (isset($request['check_id'])){

            $checkList = CheckList::on()->find($request['check_id']);
            $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
            $checkList['auth_name'] = auth()->user()->NAME;
            $checkList['auth_last_name'] = auth()->user()->LAST_NAME;
            $checkList['active_check_text'] = json_encode($request['arrCheckInput']);
            $checkList['count_view'] = $request['countView'];
            $checkList['role_check'] = json_encode($request['valueGroups']);
            $checkList->save();


            $checkReports = CheckReports::on()->where('check_id',$request['check_id'])->select('date','id','check_users_id','name','last_name')->get();



            if (!empty($checkReports)){
                foreach ($checkReports  as $checkReport){
                    if ($checkReport['date'] == date('Y-m-d')){
                        $checkReportSave = CheckReports::on()->find($checkReport['id']);
                        $checkReportSave['count_check'] = count($request['arrCheckInput']);
                        $checkReportSave['role_check_json'] = json_encode($request['valueGroups']);
                        $checkReportSave->save();
                    }else{

                      $dataBaseUser =
                          [
                            'ID'=>$checkReport['check_users_id'],
                            'NAME'=>$checkReport['name'],
                            'LAST_NAME'=>$checkReport['last_name'],
                          ];

                      $this->saveReports($checkList,$dataBaseUser,$request);
                    }
                }
            }



        }
    }

    public function viewAuthCheck(Request$request){

       if (!empty($request['auth_check'])){
           foreach (json_decode($request['auth_check']) as $key => $arrCheckInput){
               $check_list[$key] = CheckList::on()->where('id',$arrCheckInput->check_list_id)->get()->toArray();
           }
           return response($check_list);
       }
    }

    public function sendAuthCheck(Request$request)
    {


        if (!empty($request['auth_check'])){
            foreach ($request['auth_check'] as $requestCheck){
                $reports = CheckReports::on()->where('check_id',$requestCheck['id'])
                    ->where('year',date('Y'))
                    ->where('month',date('n'))
                    ->where('day',date('d'))
                    ->where('check_users_id',auth()->user()->ID)->get();

                $countChecked = [];
                foreach ($requestCheck['check_input'] as $k => $checkedCount){
                    if ($checkedCount['checked']){
                        $countChecked[$k] = $checkedCount;
                    }
                }

                if (!empty($reports) && $reports != '[]'){
                    foreach ($reports as $report){
                        $editCountCheck_auth = CheckReports::on()->find($report['id']);
                        $editCountCheck_auth['count_check_auth'] = count($countChecked);
                        $editCountCheck_auth->save();
                    }
                }else{

                    $check_reports_save = new CheckReports();
                    $check_reports_save['check_id'] = $requestCheck['id'];
                    $check_reports_save['check_users_id'] = auth()->user()->ID;
                    $check_reports_save['year'] = date('Y');
                    $check_reports_save['month'] = date('n');
                    $check_reports_save['day'] = date('d');
                    $check_reports_save['count_check_auth'] = count($countChecked);
                    $check_reports_save['count_check'] = count($requestCheck['check_input']);
                    $check_reports_save['item_type'] = 1;
                    $check_reports_save['item_id'] = $requestCheck['gr_id'];
                    $check_reports_save->save();
                }
            }
        }
    }
}
