<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Classes\Analytics\LayoffQuiz;
use Carbon\Carbon;
use App\User;
use App\UserDescription;
use App\Classes\Helpers\Phone;
use App\Models\Admin\History;
use App\Models\Bitrix\Lead;
use App\Models\Analytics\TraineeReport;
use App\Components\TelegramBot;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function loginAs(Request $request, $id) {
        $token = User::whereIn('ID', [5,18, 157 , 84])
            ->where('AUTH_TOKEN', $request->auth)
            ->first();

        if($token) {
            $user = User::find($id);

            if(empty($user->AUTH_TOKEN)){
                $token = bin2hex(random_bytes(60));
                $user->AUTH_TOKEN = $token;
                $user->save();
            }

            Auth::login($user, true);
        }

        return redirect('/');
    }

    public function manual()
    {
        if(Auth::user() && Auth::user()->ID == 5) {
            return view('manual');
        } else { 
            abort(404);
        }
        
    }

    public function quiz_after_fire(Request $request)
    {
        return view('quiz_after_fire')->with([
            'phone' => $request->phone,
            'quiz' => LayoffQuiz::getQuestions(),
        ]);
    }
    
    public function estimate_first_day(Request $request)
    {

        if ($request->isMethod('get')) {
            return view('estimate_first_day')->with([
                'phone'  => $request->phone,
             ]);
        }

        if ($request->isMethod('post')) {

            $user = User::withTrashed()->where('ID', $request->phone)->first();

            $answers = [
                '1' => $request->q1,
                '2' => $request->q2,
                '3' => $request->q3,
                '4' => $request->q4,
                '5' => $request->q5, // 5.1
                '6' => $request->q6, // 5.2
            ];

            TelegramBot::send($request->all());

            if($user) {
                $lead = Lead::where('user_id', $user->ID)->first();

                if($lead && $lead->invite_at) {
                    $date = Carbon::parse($lead->invite_at);
                    $tr = TraineeReport::where('date', $date->format('Y-m-d'))
                        ->where('group_id', $lead->invite_group_id)
                        ->first();
                    
                    if($tr) {
                        $data = $tr->data;
                        $data[$user->ID] = $answers;
                        $tr->data = $data;
                        $tr->save();
                    } else {
                        TraineeReport::create([
                            'date' => $date->format('Y-m-d'),
                            'group_id' => $lead->invite_group_id,
                            'day_1' => 0,
                            'data' => [$answers]
                        ]);
                    }   
                }
            }

            return redirect('/');
        } 
        
    }

    public function estimate_trainer(Request $request)
    {
        return view('estimate_trainer')->with([
            'phone' => $request->phone,
            'quiz' => [
                '1' => [
                    'q' => 'Оцените по 10 бальной шкале, насколько вы считаете, что тренер классно обучает:',
                    'answers' => [],
                    'type' => 'star'
                ],
            ],
        ]);
    }

    public function save_estimate_trainer(Request $request)
    {
        History::intellect('Оценка тренера', $request->all());
        
        if($request->has('phone') && $request->phone != '') {
            
            $users = User::withTrashed()->where('UF_ADMIN', 1)->get();
            
            foreach($users as $user) {
                $phone = Phone::normalize($user->PHONE);
                if($phone == Phone::normalize($request->phone)) {
                    $ud = UserDescription::where('user_id', $user->ID)->first();
                    if(!$ud) {
                        $ud = UserDescription::create([
                            'user_id' => $user->ID
                        ]);
                    }
                }
            }
          
            if($ud && $request->has('answer1')) {   
                 $rating = [
                    'head_id' => 0,
                    'rating' => $request->answer1,
                    'date' => time(),
                ];

                if($ud->rating1) {
                    $ud->rating2 = $rating;
                } else {
                    $ud->rating1 = $rating;
                }

                $ud->save();
            }


        } 
    }
    
  

    
}
