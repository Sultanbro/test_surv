<?php

namespace App\Http\Controllers\Settings;

use App\Models\CheckUsers;
use App\Models\Checklist;
use App\Models\Task;
use App\Models\CheckReports;
use App\Position;
use App\ProfileGroup;
use App\User;
use App\Models\Checkedtask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CheckListController extends Controller
{

    public function store(Request $request,$edit = null)
    {
        if ($edit === null){
             // $checklist = new Checklist();
             // $checklist->creator_id = auth()->id();
             // $checklist->title = 'checklist_'.$checklist->id;
             // $checklist->json_users = $request['allValueArray'];  
            $this->recordChecklists($request['allValueArray'], $request['arr_check_input']['tasks'], $request['countView']);
    
            return 'success';
        }else{
            dd($edit);
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
            $check_users['work_start'] = $positionUser['work_start'] ?? '09:00:00';
            $check_users['work_end'] = $positionUser['work_end'] ?? '18:00:00';
            $check_users['middleware_count'] =  0;
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
                $check_users['work_start'] = $positionUser['work_start'] ?? '09:00:00';
                $check_users['work_end'] = $positionUser['work_end'] ?? '18:00:00';
                $check_users['middleware_count'] =  0;
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


            if (!empty($users)){
                foreach ($users as $user){
                    $check_users = new CheckUsers();
                    $check_users['name'] =$user->name;
                    $check_users['last_name'] = $user->last_name;
                    $check_users['check_list_id'] = $checkList->id;
                    $check_users['check_users_id'] =  $user->id;
                    $check_users['check_reports_id'] = $this->saveReports($checkList, $user, $request, $profileGroups,$type);
                    $check_users['count_view'] = $request['countView'];
                    $check_users['item_type'] = $type;
                    $check_users['item_id'] = $profileGroups->id;
                    $check_users['middleware_count'] =  0;
                    $check_users['work_start'] =  $user->work_start ?? '09:00:00';
                    $check_users['work_end'] = $user->work_end ?? '19:00:00';
                    $check_users->save();
                }
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

        $checkList = Checklist::with('users','creator','tasks')->get();

        return $checkList->toArray();
    }

    public function deleteCheck(Request $request){

        $editedChecklist = Checklist::find($request['delete_id']);
        $tasks = $editedChecklist->tasks;
        foreach($tasks as $task){
            Checkedtask::where('task_id',$task->id)->where('created_date', Carbon::now()->toDateString())->delete();
            $task->delete();
        }
        $editedChecklist->delete();
    }

    public function editCheck(Request $request)
    {   
        $check_list = Checklist::where('id',$request['check_id'])->with('tasks')->first();
        return $check_list->toArray();
    }

    public function editSaveCheck(Request $request){

        Task::destroy($request['deleted_tasks']);
        Checkedtask::whereIn('task_id',$request['deleted_tasks'])->where('created_date',Carbon::now()->toDateString())->delete();

        $editedChecklist = Checklist::find($request['check_id']);
        $editedChecklist->show_count = $request['countView'];
        $editedChecklist->save();
        $users = $editedChecklist->users;
        if(!isset($request['allValueArray'][0]['id'])){
            foreach ($request['arr_check_input'] as $task){
                $task = Task::updateOrCreate([
                    'id' => isset($task['id']) ? $task['id'] : 0
                ],
                [

                    'task' => $task['task'],
                    'checklist_id' => $editedChecklist->id
                ]);
                Checkedtask::where('task_id',$task->id)->where('created_date',Carbon::now()->toDateString())->delete();
                foreach($users as $user){
                    $task->checkedtasks()->updateOrCreate([ 
                        'task_id' => $task->id,
                        'created_date' => Carbon::now()->toDateString(),
                        'user_id' => $user->id,                  
                    ],
                    [              
                        'checked' => 'false',
                        'url' => ''
                    ]);
                }
            }
            $checklist_data = $request['allValueArray'];
            
            $this->recordChecklists($checklist_data, $request['arr_check_input'], $request['countView']);

        }else{
            $tasks = Task::where('checklist_id',$editedChecklist->id)->get();
            foreach($tasks as $task){
                Checkedtask::where('task_id',$task->id)->where('created_date', Carbon::now()->toDateString())->delete();
            }
            $editedChecklist->delete();
            $this->recordChecklists($request['allValueArray'], $request['arr_check_input'], $request['countView']);
        }

        // Проверка на удаление пользователя
    }

    public function viewAuthCheck(Request $request){

        /*if (!empty($request['auth_check'])){
            foreach (json_decode($request['auth_check']) as $key => $arrCheckInput){


                $check_list['checklist'][$key] = CheckList::where('id',$arrCheckInput->check_list_id)->get()->toArray();
//                $check_list['check'][$key] = CheckReports::find($arrCheckInput->check_reports_id);
                $time =  $check_list['checklist'][$key][0]['show_count'];
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
            array_push($check_list,$time);
            return response($check_list);
        }*/
        $user = User::with('checklists.tasks')->find(auth()->user()->id);
        return $user->checklists;
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



    public function responsibility(Request $request){
        $valueUser = $request['search'];

        $resultsUser = User::where('email','like', "%$valueUser%")->select('id','email')->pluck('code','name')
            ->get()->toArray();




        return $resultsUser;

    }

    public function createCheckListForGroup($request){
        dd('testing');

    }

    public function recordChecklists($records, $tasks, $count_view){
            
            foreach ($records as $user_data){
                
                if(isset($user_data['type']) && $user_data['type'] == 2){//group

                    $checklist = Checklist::updateOrCreate(
                    [ 
                        'json_users' => $user_data['id'],
                    ],
                    [
                        'creator_id' => auth()->id(),
                        'title' => '',
                        'show_count' => $count_view,
                        'type' => 2,
                    ]);

                    $group = ProfileGroup::find($user_data['id']);
                    $checklist->json_users = $group->id;
                    $checklist->title = $group->name;
                    $users = User::whereIn('id',json_decode($group->users))->get();
                    foreach ($tasks as $task){
                        $task = Task::updateOrCreate([
                            'id' => isset($task['id']) ? $task['id'] : 0
                        ],[
                            'task' => $task['task'],
                            'checklist_id' => $checklist->id
                        ]);
                        foreach($users as $user){
                            $task->checkedtasks()->updateOrCreate([
                                'created_date' => Carbon::now()->toDateString(),
                                'task_id' => $task->id,
                                'user_id' => $user->id, 
                            ],
                            [      
                                             
                                'checked' => 'false',
                                'url' => ''
                            ]);
                        }
                        
                    }

                    foreach($users as $user){
                        $checklist->users()->updateOrCreate([
                            'id' => $user->id
                        ],[
                            'checklist_id' => $checklist->id,
                            'user_id' => $user->id
                        ]);


                    }

                    $checklist->save();

                }else if(isset($user_data['type']) && $user_data['type'] == 3 ){//team leaders

                    $checklist = Checklist::updateOrCreate(
                    [
                        'json_users' => $user_data['id'], 
                    ],
                    [
                        'creator_id' => auth()->id(),
                        'title' => '',
                        'show_count' => $count_view,
                        'type' => 3,
                    ]);

                    $profilePosition = Position::on()->find($user_data['id']);
                    $users = User::where('position_id',$profilePosition->id)->get();
                    foreach ($tasks as $task){
                        $mytask = null;
                        if(isset($task['id'])){
                            $mytask = Task::updateOrCreate([
                                'id' => $task['id']
                            ],[
                                'task' => $task['task'],
                                'checklist_id' => $checklist->id
                            ]);
                        }else{
                            $mytask = Task::create([
                                'task' => $task['task'],
                                'checklist_id' => $checklist->id
                            ]);
                        }
                        
                        foreach($users as $user){
                            $mytask->checkedtasks()->updateOrCreate([
                                'created_date' => Carbon::now()->toDateString(),
                                'task_id' => $mytask->id,
                                'user_id' => $user->id,                
                            ],
                            [              
                                'checked' => 'false',
                                'url' => ''
                            ]);
                        }
                    }

                    $checklist->title = $profilePosition->position;
                    $checklist->json_users = $user_data['id'];
                    foreach($users as $user){
                        $checklist->users()->updateOrCreate([
                                'id' => $user->id
                            ],[
                                'checklist_id' => $checklist->id,
                                'user_id' => $user->id
                            ]);
                    }

                    $checklist->save();
                }else if(isset($user_data['type'])){//simple users



                    $checklist = Checklist::updateOrCreate([
                        'json_users' => $user_data['id'],
                    ],
                    [
                        'creator_id' => auth()->id(),
                        'title' => '',
                        'show_count' => $count_view,
                    ]);

                    $user = User::find($user_data['id']);
                    foreach ($tasks as $task){
                        $task = Task::updateOrCreate([
                            'id' => isset($task['id']) ? $task['id'] : 0
                        ],
                        [
                            'task' => $task['task'],
                            'checklist_id' => $checklist->id
                        ]);
                        $task->checkedtasks()->updateOrCreate([
                                'created_date' => Carbon::now()->toDateString(),  
                                'task_id' => $task->id,
                                'user_id' => $user->id,      
                            ],
                            [                        
                                'checked' => 'false',
                                'url' => ''
                            ]);
                    
                        
                    }

                    $checklist->title = $user->name . ' ' . $user->last_name;
                    $checklist->users()->updateOrCreate([
                            'id' => $user_data['id']
                        ],[
                            'checklist_id' => $checklist->id,
                            'user_id' => $user->id
                        ]);
                    $checklist->save();
                }
            }
    }

    public function getTasks(Request $request){
        $checklists = Checklist::whereIn('id',$request['checklist_id'])->get();
        $tasks = [];
        $chow_counts = [];
        foreach($checklists as $checklist){
            $show_counts[] = $checklist->show_count;
            $task = Task::where('checklist_id',$checklist->id)->with('checkedtasks')->get();
            foreach($task as $t){
                if(sizeof($t->checkedtasks) == 0){
                    $t->checkedtasks[0] = [
                        //'task_id' => $task->id,
                        'url' => '',
                        'created_date' => Carbon::now()->toDateString(),
                        'checked' => 'false',
                    ];  
                }

            }
            $tasks[$checklist->title] = $task;
        }
        return [$tasks,$show_counts];
    }

    public function saveTasks(Request $request){
        foreach($request['auth_check'] as $task){
            foreach($task as $t){
                if( ($t['checkedtasks'][0]['url'] != null && $t['checkedtasks'][0]['checked'] == 'true') || ($t['checkedtasks'][0]['url'] != null && $t['checkedtasks'][0]['checked'] != null)){
                    if (filter_var($t['checkedtasks'][0]['url'], FILTER_VALIDATE_URL) === FALSE) {
                        return 3;
                    }
                    $checked_task = Checkedtask::updateOrCreate([
                        'task_id' => $t['id'],
                        'created_date' => Carbon::now()->toDateString(),
                        'user_id' => auth()->id(),
                    ],[
                        'url' => $t['checkedtasks'][0]['url'],
                        'checked' => $t['checkedtasks'][0]['checked'] == 'true' ? 'true' : 'false',
                        'user_id' => auth()->id(),
                    ]); 
                }else if($t['checkedtasks'][0]['url'] != null){
                    return 2;
                }else if($t['checkedtasks'][0]['checked'] == 'true'){   
                    return 4;
                }else{
                    if ($t['checkedtasks'][0]['url'] != null && filter_var($t['checkedtasks'][0]['url'], FILTER_VALIDATE_URL) === FALSE) {
                        return 3;
                    }
                    $checked_task = Checkedtask::updateOrCreate([
                        'task_id' => $t['id'],
                        'created_date' => Carbon::now()->toDateString(),
                        'user_id' => auth()->id(),
                    ],[
                        'url' => '',
                        'checked' => 'false',
                    ]);
                }

            }

        }
        return 1;
    }

    public function getChecklistByUser(Request $request){
        $checkedtasks = Checkedtask::where('created_date',$request['created_date'])->where('user_id',$request['user_id'])->with('task')->get();
        return $checkedtasks;
    }

    public function saveChecklist(Request $request){
        foreach($request['checklists'] as $checklist){
            $check = Checkedtask::find($checklist['id']);
            $check->checked = $checklist['checked'] == 'true' ? 'true' : 'false';
            $check->save();
        }
    }
}