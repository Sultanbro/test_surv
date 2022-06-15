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
use function Symfony\Component\Finder\name;

class CheckListController extends Controller
{

    public function searchSelected(Request$request){



        $valueUser = $request['query'];
        if ($request['type'] == 1){
            $groups = ProfileGroup::where('name','like', "%$valueUser%")->get(['id', 'name'])->pluck('name','id');
            $positions = Position::get(['id', 'position'])->pluck('position','id');
            $users = User::where(function($query) {
                $query->whereNotNull('name')->whereNotNull('last_name')->whereNotNull('email')->where('name', '!=', '')->where('last_name', '!=', '')->where('email', '!=', '');
            })->select('id','name','last_name','email')->get();
        }elseif ($request['type'] == 2){
            $groups = ProfileGroup::where('active', 1)->get(['id', 'name'])->pluck('name','id');
            $positions =  Position::where('position','like', "%$valueUser%")->get(['id', 'position'])->pluck('position','id');
            $users = User::where(function($query) {
                $query->whereNotNull('name')->whereNotNull('last_name')->whereNotNull('email')->where('name', '!=', '')->where('last_name', '!=', '')->where('email', '!=', '');
            })->select('id','name','last_name','email')->get();
        }elseif($request['type'] == 3){




            $groups = ProfileGroup::where('active', 1)->get(['id', 'name'])->pluck('name','id');
            $positions = Position::get(['id', 'position'])->pluck('position','id');
            $users = User::where('name','like', "%$valueUser%")
                ->orWhere('last_name','like', "%$valueUser%")
                ->select('id','name','last_name','email')->get();
        }



        return response(['users'=>$users,'positions'=>$positions,'groups'=>$groups]);





    }

    public function responsibility(Request $request){
        $valueUser = $request['search'];

        $resultsUser = User::where('email','like', "%$valueUser%")->select('id','email')->pluck('code','name')
            ->get()->toArray();




        return $resultsUser;

    }

    public function store(Request $request,$edit = null){
       
        
        if ($edit === null){
            foreach ($request['allValueArray'] as $allValidate){
                $validate = CheckList::where('item_id',$allValidate['code'])->where('item_type',$allValidate['type'])->get()->toArray();
                if (!empty($validate)){
                    return response(['success'=>false,'exists'=>$validate]);
                }
            }
        }else{
            $request = $edit;
        }


        if ($request['countView'] < 11 && $request['countView'] != 0){
            if (isset($request['allValueArray'])){

                foreach ($request['allValueArray'] as $allValueArray){
                        if ($allValueArray['type'] == 1){
                            $profileGroups = ProfileGroup::on()->find($allValueArray['code']);
                            $checkList = new CheckList();
                            $checkList['title'] = $profileGroups->name;
                            $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                            $checkList['auth_name'] = auth()->user()->name;
                            $checkList['auth_last_name'] = auth()->user()->last_name;
                            $checkList['active_check_text'] = json_encode($request['arr_check_input']);
                            $checkList['count_view'] = $request['countView'];
                            $checkList['item_type'] = $allValueArray['type'];
                            $checkList['item_id'] = $profileGroups->id;
                            $checkList->save();

                         
                            $this->saveGroup($profileGroups,$checkList,$request,1);
                        }elseif ($allValueArray['type'] == 2){
                            $profilePosition = Position::on()->find($allValueArray['code']);
                            $checkList = new CheckList();
                            $checkList['title'] = $profilePosition['position'];
                            $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                            $checkList['auth_name'] = auth()->user()->name;
                            $checkList['auth_last_name'] = auth()->user()->last_name;
                            $checkList['active_check_text'] =json_encode($request['arr_check_input']);
                            $checkList['count_view'] = $request['countView'];
                            $checkList['item_type'] =  $allValueArray['type'];
                            $checkList['item_id'] = $profilePosition->id;
                            $checkList->save();
                            $this->savePosition($profilePosition, $checkList, $request, 2);
                        }elseif ($allValueArray['type'] == 3){
                            $profileUsers = User::on()->find($allValueArray['code']);
                            if (!empty($profileUsers)) {
                                $checkList = new CheckList();
                                $checkList['title'] = $profileUsers['last_name'].' '.$profileUsers['name'];
                                $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                                $checkList['auth_name'] = auth()->user()->name;
                                $checkList['auth_last_name'] = auth()->user()->last_name;
                                $checkList['active_check_text'] = json_encode($request['arr_check_input']);
                                $checkList['count_view'] = $request['countView'];
                                $checkList['item_type'] =  $allValueArray['type'];
                                $checkList['item_id'] = $profileUsers['id'];
                                $checkList->save();
                                $this->saveUsers($profileUsers, $checkList, $request, 3);
                            }
                        }
                    }
            }
        }

        
    }

    public function saveUsers($positionUser,$checkListId,$request,$type){

        if (!empty($positionUser)){
            $check_users = new CheckUsers();
            $check_users['name'] = $positionUser['name'] ?? 'Без Имени';
            $check_users['last_name'] = $positionUser['last_name'] ?? 'Без фамилии';
            $check_users['check_list_id'] = $checkListId['id'];
            $check_users['check_users_id'] = $positionUser['id'];
            $check_users['check_reports_id'] = $this->saveReports($checkListId,$positionUser,$request,$positionUser,$type);
            $check_users['count_view'] = $request['countView'];
            $check_users['item_type'] = $type;
            $check_users['item_id'] = $positionUser['id'];
            $check_users->save();
        }


    }

    public function savePosition($profileGroups,$checkListId,$request,$type){
        $positionUsers = User::on()->where('position_id',$profileGroups->id)->get()->toArray();

        if (!empty($positionUsers)){
            foreach ($positionUsers as $positionUser){
                $check_users = new CheckUsers();
                $check_users['name'] = $positionUser['name'] ?? 'Без имени';
                $check_users['last_name'] = $positionUser['last_name'] ?? 'Без фамилии';
                $check_users['check_list_id'] = $checkListId['id'];
                $check_users['check_users_id'] = $positionUser['id'];
                $check_users['check_reports_id'] = $this->saveReports($checkListId,$positionUser,$request,$profileGroups,$type);
                $check_users['count_view'] = $request['countView'];
                $check_users['item_type'] = $type;
                $check_users['item_id'] = $profileGroups->id;
                $check_users->save();
            }
        }


    }

    public function saveGroup($profileGroups,$checkList,$request,$type)
    {
       
        if (!empty($profileGroups['id'])){

            //$dataBaseUser = User::with('user_description')
                // ->whereHas('user_description', function ($query) {
                //     $query->where('is_trainee', 0)
                // })

            $users = User::whereIn('id', json_decode($profileGroups['users']))->select(['id','name','last_name'])->get(['id','name','last_name']);

            foreach ($users as $user) {
                $check_users = new CheckUsers();

                CheckUsers::create([
                    'name'=> $user->name,
                    'last_name'=> $user->last_name,
                    'check_list_id'=> $checkList->id,
                    'check_users_id'=> $user->id,
                    'check_reports_id'=> $this->saveReports($checkList, $user, $request, $profileGroups,$type),
                    'count_view'=> $request['countView'],
                    'item_type'=> $type,
                    'item_id'=> $profileGroups->id,
                ]);
            }
        }

       
    }

    public function saveReports($checkList=null, $dataBaseUser=null, $request=null, $profileGroups=null, $type)
    {

        $check_reports_save = new CheckReports();
        $check_reports_save['check_id'] = $checkList->id ?? $checkList['id'];
        $check_reports_save['check_users_id'] = $dataBaseUser['id'] ?? $dataBaseUser->id ;
        $check_reports_save['year'] = date('Y');
        $check_reports_save['month'] = date('n');
        $check_reports_save['day'] = date('d');
        $check_reports_save['count_check'] = count($request['arr_check_input']);
        $check_reports_save['count_check_auth'] = 0;
        $check_reports_save['checked'] = json_encode($request['arr_check_input']);
        $check_reports_save['item_type'] = $type;
        $check_reports_save['item_id'] = $profileGroups['id'] ?? $profileGroups->id;
        $check_reports_save->save();

        return $check_reports_save->id;
    }

    public function listViewCheck()
    {

        $checkList = CheckList::on()->get()->toArray();

        return $checkList;
    }

    public function deleteCheck(Request $request){


        CheckList::on()->find($request['delete_id'])->delete();
        CheckReports::on()->where('check_id',$request['delete_id'])->delete();
        CheckUsers::on()->where('check_list_id',$request['delete_id'])->delete();

    }

    public function editCheck(Request $request)
    {   
        $check_list = CheckList::on()->find($request['check_id'])->toArray();
       
        return response($check_list);
    }

    public function editSaveCheck(Request $request){




        if (!empty($request['allValueArray'])){


           if (count($request['allValueArray']) > 1){
               foreach ($request['allValueArray'] as $keys =>$allValueArray) {
                   if ($request['valueFindGr'] != $allValueArray['code']) {
                       $newArrays['allValueArray'][] = $allValueArray;
                       $validate = CheckList::where('item_id', $allValueArray['code'])->where('item_type', $allValueArray['type'])->get()->toArray();
                       if (!empty($validate)) {
                           return response(['success' => false, 'exists' => $validate]);
                       }
                   }
               }
               $newArrays['countView'] = $request['countView'];
               $newArrays['arr_check_input'] = $request['arr_check_input'];
//               $newArrays['allValueArray'] = $request['allValueArray'];
               $this->store($request,$newArrays);
           }










           if (!empty($request['valueFindGr'])){



               $findArray = CheckList::find($request['check_id']);
               $findArray->auth_id = auth()->user()->id;
               $findArray->auth_name = auth()->user()->name;
               $findArray->auth_last_name = auth()->user()->last_name;
               $findArray->active_check_text = json_encode($request['arr_check_input']);
               $findArray->count_view = $request['countView'];
               $findArray->save();


               $checkReports = CheckReports::on()->where('item_id',$findArray->item_id)->where('item_type',$findArray->item_type)
                   ->where('year',date('Y'))
                   ->where('month',date('n'))
                   ->where('day',date('d'))
                   ->get()->toArray();

               if (!empty($checkReports) && count($checkReports) > 0){
                   foreach ($checkReports  as $checkReport){
                       $checkReportSave = CheckReports::on()->find($checkReport['id']);
                       $checkReportSave['count_check'] = count($request['arr_check_input']);
                       $checkReportSave->save();
                   }
               }

               if ($findArray->item_type == 1){
                   if (empty($checkReports) && count($checkReports) == 0){
                       $profileGroups = ProfileGroup::on()->find($request['valueFindGr']);
                       if (!empty($profileGroups)){
                           foreach (json_decode($profileGroups['users']) as $profile_users_id){
                               $dataBaseUser = User::on()->find($profile_users_id);
                               if (!empty($dataBaseUser)){
                                   $check_reports_save = new CheckReports();
                                   $check_reports_save['check_id'] = $findArray->id;
                                   $check_reports_save['check_users_id'] = $dataBaseUser->id ;
                                   $check_reports_save['year'] = date('Y');
                                   $check_reports_save['month'] = date('n');
                                   $check_reports_save['day'] = date('d');
                                   $check_reports_save['count_check'] = count($request['arr_check_input']);
                                   $check_reports_save['count_check_auth'] = 0;
                                   $check_reports_save['checked'] = json_encode($request['arr_check_input']);
                                   $check_reports_save['item_type'] = $findArray->item_type;
                                   $check_reports_save['item_id'] = $findArray->item_id;
                                   $check_reports_save->save();

                               }
                           }
                       }
                   }
               }elseif ($findArray->item_type == 2){
                       $positionUsers = User::on()->where('position_id',$request['valueFindGr'])->get()->toArray();
                       if (!empty($positionUsers)){
                       $check_reports_save = new CheckReports();
                       $check_reports_save['check_id'] = $findArray->id;
                       $check_reports_save['check_users_id'] = $positionUsers['id'] ;
                       $check_reports_save['year'] = date('Y');
                       $check_reports_save['month'] = date('n');
                       $check_reports_save['day'] = date('d');
                       $check_reports_save['count_check'] = count($request['arr_check_input']);
                       $check_reports_save['count_check_auth'] = 0;
                       $check_reports_save['checked'] = json_encode($request['arr_check_input']);
                       $check_reports_save['item_type'] = $findArray->item_type;
                       $check_reports_save['item_id'] = $findArray->item_id;
                       $check_reports_save->save();
                   }
               }elseif ($findArray->item_type == 3){
//                   saveReports($checkListId,$positionUser,$request,$positionUser,$type);
                   $check_reports_save = new CheckReports();
                   $check_reports_save['check_id'] = $findArray->id;
                   $check_reports_save['check_users_id'] = $request['valueFindGr'] ;
                   $check_reports_save['year'] = date('Y');
                   $check_reports_save['month'] = date('n');
                   $check_reports_save['day'] = date('d');
                   $check_reports_save['count_check'] = count($request['arr_check_input']);
                   $check_reports_save['count_check_auth'] = 0;
                   $check_reports_save['checked'] =json_encode($request['arr_check_input']);
                   $check_reports_save['item_type'] = $findArray->item_type;
                   $check_reports_save['item_id'] = $findArray->item_id;
                   $check_reports_save->save();
               }
           }

        }



    }

    public function viewAuthCheck(Request$request){




        if (!empty($request['auth_check'])){
            foreach (json_decode($request['auth_check']) as $key => $arrCheckInput){
                $check_list['checklist'][$key] = CheckList::on()->where('id',$arrCheckInput->check_list_id)->get()->toArray();
//                $check_list['check'][$key] = CheckReports::find($arrCheckInput->check_reports_id);


                $check_list['check_day'][$key] = CheckReports::on()
                    ->where('item_type',$check_list['checklist'][$key][0]['item_type'])
                    ->where('item_id',$check_list['checklist'][$key][0]['item_id'])
                    ->where('check_id',$check_list['checklist'][$key][0]['id'])
                    ->where('year',date('Y'))
                    ->where('month',date('n'))
                    ->where('day',date('d'))
                    ->get()->toArray();



                if (!empty($check_list['check_day'][$key])){

                    $title =  $check_list['checklist'][$key][0]['title'];
                    $check_list['checklist'][$key] = $check_list['check_day'][$key];
                    $check_list['checklist'][$key][0]['flag'] = true;
                    $check_list['checklist'][$key][0]['title'] =  $title;

                }else{
                    $check_list['checklist'][$key][0]['flag'] = false;
                }




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
                    ->where('check_users_id',auth()->user()->id)->get();

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
                        $editCountCheck_auth['item_type'] = $requestCheck['type'];
                        $editCountCheck_auth['item_id'] = $requestCheck['gr_id'];
                        $editCountCheck_auth['checked'] = json_encode($requestCheck['check_input']);
                        $editCountCheck_auth->save();
                    }
                }else{

                    $check_reports_save = new CheckReports();
                    $check_reports_save['check_id'] = $requestCheck['id'];
                    $check_reports_save['check_users_id'] = auth()->user()->id;
                    $check_reports_save['year'] = date('Y');
                    $check_reports_save['month'] = date('n');
                    $check_reports_save['day'] = date('d');
                    $check_reports_save['count_check'] = count($requestCheck['check_input']);
                    $check_reports_save['count_check_auth'] = count($countChecked);
                    $check_reports_save['item_type'] = $requestCheck['type'];
                    $check_reports_save['item_id'] = $requestCheck['gr_id'];
                    $check_reports_save['checked'] = json_encode($requestCheck['check_input']);
                    $check_reports_save->save();
                }
            }
        }
    }


    public function getModal(Request$request){

       $users = User::where(function($query) {
                 $query->whereNotNull('name')->whereNotNull('last_name')->whereNotNull('email')->where('name', '!=', '')->where('last_name', '!=', '')->where('email', '!=', '');
                 })->select('id','name','last_name','email')->take(200)->get();


        $positions = Position::get(['id', 'position'])->pluck('position','id');

       $groups = ProfileGroup::where('active', 1)->get(['id', 'name'])->pluck('name','id');


       return response(['users'=>$users,'positions'=>$positions,'groups'=>$groups]);

    }


}