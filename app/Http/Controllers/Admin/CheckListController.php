<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckList;
use App\ProfileGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CheckListController extends Controller
{


    public function store(Request $request)
    {


        if (isset($request['valueGroups']) && !empty($request['valueGroups'])){

            foreach ($request['valueGroups'] as $group){


                $profileGroups = ProfileGroup::on()->find($group['code']);

                return $profileGroups;


            }
        }






       if ($request['countView'] < 11 && $request['countView'] != 0){
           foreach ($request['arrCheckInput'] as $key => $item){
               if ($item['checked'] == true){
                   $checkList = new CheckList();
                   $checkList['auth_id'] = auth()->user()->getAuthIdentifier();
                   $checkList['name'] = auth()->user()->NAME;
                   $checkList['last_name'] = auth()->LAST_NAME;
                   $checkList['active_check_text'] = $item['text'] ?? 0;
                   $checkList['https'] = $item['https'] ?? 0;
                   $checkList['count_view'] = $request['countView'] ?? 0;
                   $checkList['position_id'] = $item['user_id'] ?? 0;
                   $checkList['group_id'] = $item['user_id'] ?? 0;
                   $checkList['user_id'] = $item['user_id'] ?? 0;
                   $checkList->save();
               }
           }

       }

    }

    public function listViewCheck()
    {
        $checkList = CheckList::on()->get()->toArray();

       return $checkList;
    }

    public function deleteCheck(Request$request){



        CheckList::on()->find($request['delete_id'])->delete();

    }
}
