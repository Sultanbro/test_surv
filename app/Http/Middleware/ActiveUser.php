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

class ActiveUser

{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle($request, Closure $next)
    {

        
        $user = auth()->user();
        if ($user) {
//           $kis =  Hash::make('12345678910');




            if (auth()->user()->getCheckList->toArray()){

//                $mytime = Carbon::now();
//                dd($mytime->toDateTimeString());
//                $now = new DateTime(date('h:i:s'));
//                $the_interval = new DateInterval('h:i:s');
//                $now->add($the_interval);
//
//
//
//                dd($now);
//
//
//
//
//
//
//                $diff = Carbon::parse('9:00')->getTimestamp() - Carbon::now()->getTimestamp();
//                $time = Carbon::parse($diff)->format('m:s');
//                $minutes = Carbon::parse($diff)->format('m');
//                $seconds = Carbon::parse($diff)->format('s');
//
//                dd($diff,$time,$minutes,$seconds,date('h:i:s'));
//
//               $kis = Carbon::parse('9:00:00')->diff(Carbon::now())->format('%i minutes');
//
//
//                   dd($kis,date('h:i:s'));

                foreach (auth()->user()->getCheckList->toArray() as $authTime){



                    if (date('h:i:s') >= $authTime['middleware_auth']){
                        $сheck_user = CheckUsers::find($authTime['id']);
                        $сheck_user['middleware_auth'] = date('h:i:s');



//                        dd($authTime,'ss');
//                        $сheck_user->save();



                    }else{


//                        dd($authTime,'qq');


                    }
                }
            }
//            dd($user,date('d-m-'));
//            $user->visited = Carbon::now();



        }

        return $next($request);

    }
}
