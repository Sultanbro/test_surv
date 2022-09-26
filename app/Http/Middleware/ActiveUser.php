<?php

namespace App\Http\Middleware;
use App\Models\CheckUsers;
use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DateTime;
use DateInterval;
use phpDocumentor\Reflection\Type;
use function Ramsey\Uuid\v1;

class ActiveUser

{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    function monthToWeeks($month, $year)
    {
        $lastDay = date('d.m.Y', mktime(0, 0, 0, $month+1, 1, $year));
        $weeks = [date('d.m.Y', mktime(0,0,0, $month, 1, $year))];





        for ($i = 2; $i < date('t'); $i++) {

            $t = mktime(0, 0, 0, $month, $i, $year);


            if (date('N', $t) == 1) {
                array_push($weeks, date('d.m.Y', $t));
            }


        }









        array_push($weeks, $lastDay);
        $countOfWeeks = count($weeks) - 1;



        $i = 0;

        while ($i < $countOfWeeks) {
            $kis[$i][] = $weeks[$i];
            $kis[$i][1] = $weeks[$i+1];

            $i++;
        }





    }

    public function mon($month,$year){

        $weeks = [date('d.m.Y', mktime(0,0,0, $month, 1, $year))];
        $lastDay = date('d.m.Y', mktime(0, 0, 0, $month+1, 1, $year));








    }


    public function handle($request, Closure $next)
    {
        if($request->getPathInfo() =='/logout') return $next($request);
        //        $kis2 = $this->mon(6,2022);






        //        $kis = Carbon::parse($request->datestart)->format('m');





        //        $carbon = new Carbon();
        //        echo $carbon->addWeeks(3);
        //       echo $carbon->addWeek();
        //       echo $carbon->subWeek();
        //       echo $carbon->subWeeks(3);






        if (auth()->user()){

            auth()->user()->show_checklist = 0;
            $user = auth()->user();
            /*
              if (!empty(auth()->user()->checklists->toArray())){
                  foreach (auth()->user()->checklists->toArray() as $user_check_list){
                      $editUser_check_list = CheckUsers::find($user_check_list['id']);



                      if (!empty($editUser_check_list['work_end']) && !empty($editUser_check_list['work_start'])){
                          $editUser_check_list['middleware_auth'] = date('h:i:s');

                          $work_Start = $editUser_check_list['middleware_auth'];
                          $work_start_h = substr("$work_Start ",0 , 2);
                          $work_start_m = substr("$work_Start ",3 , 2);
                          $work_start_s = substr("$work_Start ",6 , 2);

                          $work_End = $editUser_check_list['work_end'];
                          $work_end_h = substr("$work_End ",0 , 2);
                          $work_end_m = substr("$work_End ",3 , 2);
                          $work_end_s = substr("$work_End ",6 , 2);

                          $dtStart= Carbon::createFromTime($work_start_h,$work_start_m,$work_start_s);
                          $dtEnd =  Carbon::createFromTime('19','00','00');
                          $minut = $dtStart->diffInMinutes($dtEnd);
                          $share_minut = $minut / $user_check_list['show_count'];

//                          dd($work_end_h,$work_end_m,$work_end_s);
                      }else{
                          $dtStart= Carbon::createFromTime('09','00','00');
                          $dtEnd =  Carbon::createFromTime('19','00','00');

                          $minut = $dtStart->diffInMinutes($dtEnd);
                          $share_minut = $minut / $user_check_list['show_count'];
                      }



                          $current = Carbon::now();
                          $trialExpires = $current->addMinute($share_minut);
                          $trialExpires->toTimeString();



                      if ($editUser_check_list['middleware_count'] == 0){

                          $editUser_check_list['middleware_count'] = 1;
                          $editUser_check_list['middleware_next_time'] = $trialExpires->toTimeString();
                          $editUser_check_list->save();

                          auth()->user()->show_checklist = 1;
                      }else{
                          if ($editUser_check_list['count_view'] > $editUser_check_list['middleware_count']){

                              if ($editUser_check_list['middleware_next_time'] === date('h:i:s')){

                                  $middleware_count = $editUser_check_list['middleware_count'] +1;
                                  $editUser_check_list['middleware_count'] = $middleware_count;
                                  $editUser_check_list['middleware_next_time'] = $trialExpires->toTimeString();
                                  $editUser_check_list->save();

                                  auth()->user()->show_checklist = 1;
                              }


                          }else if ($editUser_check_list['count_view'] === $editUser_check_list['middleware_count']){

                              $editUser_check_list['middleware_count'] = 0;
                              $editUser_check_list['middleware_next_time'] = 0;
                              $editUser_check_list->save();

                              auth()->user()->show_checklist = 1;
                          }

                        }
                      }
                  }else{
                auth()->user()->show_checklist = 0;
             }*/
        }

        return $next($request);

    }
}
