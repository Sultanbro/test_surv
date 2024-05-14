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

class PageController extends Controller
{
    public function __construct()
    {

    }

    public function home() {
        return view('home');
    }

    public function newprofile()
    {
        // admin.jobtron.org
        if (request()->getHost() === 'admin.' . config('app.domain')) {
            return view('admin');
        }

        return view('newprofile');
    }

    /**
     * login to test.jobtron.org
     * to see antoher profile
     */
    public function loginAs(Request $request, $id)
    {
        if(tenant('id') == 'bp') {
            $token = User::whereIn('id', [5,18, 3423, 84])
                ->where('remember_token', $request->auth)
                ->first();

            if($token) {
                $user   = User::find($id);

                $admins = User::where('is_admin', 1)->get('id')->pluck('id')->toArray();

                if(in_array($id, $admins) && $token->is_admin != 1) {
                    return redirect('/');
                }

                if(empty($user->remember_token)){
                    $token                = bin2hex(random_bytes(60));
                    $user->remember_token = $token;
                    $user->save();
                }

                Auth::login($user, true);
            }
        }

        return redirect('/');
    }

    /**
     * quiz to fired users
     */
    public function quiz_after_fire(Request $request)
    {
        return view('specific.quiz_after_fire')->with([
            'phone' => $request->phone,
            'quiz' => LayoffQuiz::getQuestions(),
        ]);
    }

    /**
     * estimate first day for trainees
     */
    public function estimate_first_day(Request $request)
    {

        if ($request->isMethod('get')) {
            return view('specific.estimate_first_day')->with([
                'phone'  => $request->phone,
             ]);
        }

        if ($request->isMethod('post')) {

            $user = User::withTrashed()->where('id', $request->phone)->first();

            $answers = [
                '1' => $request->q1,
                '2' => $request->q2,
                '3' => $request->q3,
                '4' => $request->q4,
                '5' => $request->q5, // 5.1
                '6' => $request->q6, // 5.2
            ];

            if($user) {
                $lead = Lead::where('user_id', $user->id)->first();

                if($lead && $lead->invite_at) {
                    $date = Carbon::parse($lead->invite_at);
                    $tr = TraineeReport::where('date', $date->format('Y-m-d'))
                        ->where('group_id', $lead->invite_group_id)
                        ->first();

                    if($tr) {
                        $data = $tr->data;
                        $data[$user->id] = $answers;
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

    /**
     * estimate trainer for trainees
     */
    public function estimate_trainer(Request $request)
    {
        return view('specific.estimate_trainer')->with([
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

    /**
     * save estimation
     */
    public function save_estimate_trainer(Request $request)
    {
        History::intellect('Оценка тренера', $request->all());

        if($request->has('phone') && $request->phone != '') {

            $users = User::withTrashed()->get();

            foreach($users as $user) {
                $phone = Phone::normalize($user->phone);
                if($phone == Phone::normalize($request->phone)) {
                    $ud = UserDescription::where('user_id', $user->id)->first();
                    if(!$ud) {
                        $ud = UserDescription::create([
                            'user_id' => $user->id
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
