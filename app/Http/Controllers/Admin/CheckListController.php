<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckUsers;
use App\Models\CheckList;
use App\Models\CheckReports;
use App\Position;
use App\ProfileGroup;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Artisan;

class CheckListController extends Controller
{


    public function validateRequest($query)
    {
        $validateGR = [];
        foreach ($query as $keys => $group){
            $validateGR = CheckList::on()->where('item_id',$group['code'])->get()->toArray();
        }

        return count($validateGR);
    }

    public function store(Request $request){

       if ($request['countView'] < 11 && $request['countView'] != 0){







           if (count($request['valueGroups']) != 0 && !empty($request['valueGroups']) && $request['checked']['gr'] == 1){
                $countCheckList = $this->validateRequest($request['valueGroups']);
                if ($countCheckList == 0){
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
                        $this->saveGroup($profileGroups,$checkList,$request,1);
                    }
                }else{
                    return response(['successGR'=>false]);
                }
           }

           if (count($request['valuePositions']) != 0 && !empty($request['valuePositions']) && $request['checked']['ps'] == 1){
               $countCheckList = $this->validateRequest($request['valuePositions']);
               if ($countCheckList == 0) {
                   foreach ($request['valuePositions'] as $keys => $group) {
                       $profileGroups = Position::on()->find($group['code']);
                       if (!empty($profileGroups)) {
                           $checkList = new CheckList();
                           $checkList['title'] = $profileGroups['position'];
                           $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                           $checkList['auth_name'] = auth()->user()->NAME;
                           $checkList['auth_last_name'] = auth()->user()->LAST_NAME;
                           $checkList['active_check_text'] = json_encode($request['arrCheckInput']);
                           $checkList['count_view'] = $request['countView'];
                           $checkList['item_type'] = 2;
                           $checkList['item_id'] = $profileGroups['id'];
                           $checkList->save();
                           $this->savePosition($profileGroups, $checkList, $request, 2);
                       }
                   }
               }else{
                   return response(['successPS'=>false]);
               }
           }

           if (count($request['valueUsers']) != 0 && !empty($request['valueUsers']) && $request['checked']['us'] == 1){

               $countCheckList = $this->validateRequest($request['valueUsers']);



               if ($countCheckList == 0) {
                   foreach ($request['valueUsers'] as $keys => $group) {
                       $profileUsers = User::on()->find($group['code']);
                       if (!empty($profileUsers)) {
                           $checkList = new CheckList();
                           $checkList['title'] = $profileUsers['LAST_NAME'].' '.$profileUsers['NAME'];
                           $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                           $checkList['auth_name'] = auth()->user()->NAME;
                           $checkList['auth_last_name'] = auth()->user()->LAST_NAME;
                           $checkList['active_check_text'] = json_encode($request['arrCheckInput']);
                           $checkList['count_view'] = $request['countView'];
                           $checkList['item_type'] = 3;
                           $checkList['item_id'] = $profileUsers['ID'];
                           $checkList->save();
                           $this->saveUsers($profileUsers, $checkList, $request, 3);
                       }
                   }
               }else{
                   return response(['successUs'=>false]);
               }

           }
       }
    }


    public function saveUsers($positionUser,$checkListId,$request,$type){

        if (!empty($positionUser)){
                $check_users = new CheckUsers();
                $check_users['name'] = $positionUser['NAME'];
                $check_users['last_name'] = $positionUser['LAST_NAME'];
                $check_users['check_list_id'] = $checkListId['id'];
                $check_users['check_users_id'] = $positionUser['ID'];
                $check_users['check_reports_id'] = $this->saveReports($checkListId,$positionUser,$request,$positionUser,$type);
                $check_users['count_view'] = $request['countView'];
                $check_users['item_type'] = $type;
                $check_users['item_id'] = $positionUser['ID'];
                $check_users->save();
        }


    }

    public function savePosition($profileGroups,$checkListId,$request,$type){
        $positionUsers = User::on()->where('position_id',$profileGroups['id'])->get()->toArray();

        if (!empty($positionUsers)){
            foreach ($positionUsers as $positionUser){
                $check_users = new CheckUsers();
                $check_users['name'] = $positionUser['NAME'];
                $check_users['last_name'] = $positionUser['LAST_NAME'];
                $check_users['check_list_id'] = $checkListId['id'];
                $check_users['check_users_id'] = $positionUser['ID'];
                $check_users['check_reports_id'] = $this->saveReports($checkListId,$positionUser,$request,$profileGroups,$type);
                $check_users['count_view'] = $request['countView'];
                $check_users['item_type'] = $type;
                $check_users['item_id'] = $profileGroups['id'];
                $check_users->save();
            }
        }


    }

    public function saveGroup($profileGroups,$checkList,$request,$type)
    {


        if (!empty($profileGroups['id'])){
            foreach (json_decode($profileGroups['users']) as $profile_users_id){
                if (!empty($profile_users_id)){
                    $dataBaseUser = User::on()->find($profile_users_id);
                    if (!empty($dataBaseUser)){
                        $check_users = new CheckUsers();
                        $check_users['name'] = $dataBaseUser['NAME'];
                        $check_users['last_name'] = $dataBaseUser['LAST_NAME'];
                        $check_users['check_list_id'] = $checkList->id;
                        $check_users['check_users_id'] = $dataBaseUser['ID'];
                        $check_users['check_reports_id'] = $this->saveReports($checkList,$dataBaseUser,$request,$profileGroups,$type);
                        $check_users['count_view'] = $request['countView'];
                        $check_users['item_type'] = $type;
                        $check_users['item_id'] = $profileGroups['id'];
                        $check_users->save();
                      }
                    }
                }
            }
        }

    public function saveReports($checkList=null,$dataBaseUser=null,$request=null,$profileGroups=null,$type)
    {

        $check_reports_save = new CheckReports();
        $check_reports_save['check_id'] = $checkList->id ?? $checkList['id'];
        $check_reports_save['check_users_id'] = $dataBaseUser['ID'];
        $check_reports_save['year'] = date('Y');
        $check_reports_save['month'] = date('n');
        $check_reports_save['day'] = date('d');
        $check_reports_save['count_check'] = count($request['arrCheckInput']);
        $check_reports_save['count_check_auth'] = 0;
        $check_reports_save['item_type'] = $type;
        $check_reports_save['item_id'] = $profileGroups['id'] ?? $profileGroups['ID'];
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
        CheckReports::on()->where('check_id',$request['delete_id'])->delete();
        CheckUsers::on()->where('check_list_id',$request['delete_id'])->delete();

    }

    public function editCheck(Request$request)
    {
        $check_list = CheckList::on()->find($request['check_id'])->toArray();

        return $check_list;
    }

    public function editSaveCheck(Request$request){


        if (isset($request['check_id'])){

            if (!empty($request['valueGroups'])){
                foreach ($request['valueGroups'] as $keys => $valueGroup){
                    if ($valueGroup['code'] != $request['valueFindGr']){
                        $dataBaseCheckList = CheckList::on()->where('item_id',$valueGroup['code'])->get()->toArray();
                        if (!empty($dataBaseCheckList)){
                            foreach ($dataBaseCheckList as $dataBaseGr){
                                if ($dataBaseGr['item_type'] == 1){
                                    $text = "Данная Группа ".$dataBaseGr['title'].' уже существует';
                                }elseif ($dataBaseGr['item_type'] == 2){
                                    $text = "Должность ".$dataBaseGr['title'].' уже существует';
                                }
                                return response(['type'=>1,'item'=>$text]);
                            }
                        }
                    }
                }
            }
            $checkList = CheckList::on()->find($request['check_id']);
            $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
            $checkList['auth_name'] = auth()->user()->NAME;
            $checkList['auth_last_name'] = auth()->user()->LAST_NAME;
            $checkList['active_check_text'] = json_encode($request['arrCheckInput']);
            $checkList['count_view'] = $request['countView'];
            $checkList->save();

//            $profileGroups,$checkList,$request




            foreach ($request['valueGroups'] as $keys => $valueGroup){

                $profileGroups = ProfileGroup::on()->find($valueGroup['code']);

                if ($valueGroup['code'] == $request['valueFindGr']) {
                    $checkReports = CheckReports::on()->where('item_id',$valueGroup['code'])
                        ->where('year',date('Y'))
                        ->where('month',date('n'))
                        ->where('day',date('d'))
                        ->get()->toArray();
                    if (!empty($checkReports)){
                        foreach ($checkReports  as $checkReport){
                            $checkReportSave = CheckReports::on()->find($checkReport['id']);
                            $checkReportSave['count_check'] = count($request['arrCheckInput']);
                            $checkReportSave->save();
                        }
                    }else{
                        if (!empty($profileGroups)){
                            foreach (json_decode($profileGroups['users']) as $profile_users_id){
                                $dataBaseUser = User::on()->find($profile_users_id)->toArray();
                                $this->saveReports($checkList,$dataBaseUser,$request,$profileGroups);
                            }
                        }
                    }
                }else{
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
                    $this->saveGroup($profileGroups,$checkList,$request,1);
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
