<?php

namespace App\Http\Controllers\Admin;

use DB;
use View;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ProfileGroup;
use App\EstimateGrade;
use App\AnalyticsSettings;
use App\AnalyticsSettingsIndividually;
use App\Models\Analytics\TopValue;
use App\Models\Analytics\Proceed;
use App\Classes\Analytics\PrCstll;
use App\Classes\Analytics\Ozon;
use App\Classes\Analytics\DM;
use App\Classes\Analytics\HomeCredit;
use App\Classes\Analytics\Eurasian;
use App\Classes\Analytics\Kaspi;
use App\Classes\Analytics\Impl;
use App\Classes\Analytics\Recruiting as RM;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Models\Analytics\Activity;

class NpsController extends Controller
{

    public function __construct()
    {
        View::share('title', 'NPS');
        $this->middleware('auth');
    }

    /**
     * Страница
     */
    public function index()
    {
        View::share('menu', 'timetrackingnps');

        $superusers = [5,18, 157];
        $users = User::where('roles', 'like', '%"page-nps":"on"%')->pluck('id')->toArray();
        $users = array_unique(array_merge($users, $superusers));

        $has_access = in_array(Auth::user()->id, $users);
        if(!$has_access) {
            abort(404);
        }

        return view('admin.nps');
    }

    /**
     * Страница  axios
     * @method POST
     */
    public function fetch(Request $request)
    {
        $superusers = User::where('is_admin', 1)->get(['id'])->pluck('id')->toArray();
        $users = User::where('roles', 'like', '%"page-top":"nps"%')->pluck('id')->toArray();
        $users = array_unique(array_merge($users, $superusers));

        $has_access = in_array(Auth::user()->id, $users);
        if(!$has_access) {
            abort(404);
        }

        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');



        $users = [];

        $_users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('position_id', [45,55]) // Руководитель, старший специалист группы
            ->get(['users.id', 'users.name', 'users.last_name', 'users.position_id']);

        foreach($_users as $user) {


            if($user->position_id == 45) {
                $groups = ProfileGroup::headIn($user->id, false);
                $group = count($groups) > 0 ? $groups[0]->name : '.Без группы';
            } else {
                $_user = User::withTrashed()->find($user->id);
                $groups = $_user->inGroups();
                $group = count($groups) > 0 ? $groups[0]->name : '.Без группы';
            }


            $arr = [
                'name' => $user->last_name . ' '. $user->name,
                'group_id' => $group,
                'position' => $user->position_id == 45 ? 'Руковод' : 'Cт. спец',
                'texts' => [],
                'minuses' => [],
            ];

            for($i = 1; $i <=12; $i++) {
                $m = Carbon::createFromDate($request->year, $i, 1)->format('Y-m-d');
                $es_grades = EstimateGrade::where('date', $m)
                    ->where('about_id', $user->id)
                    ->get();

                $grade = 0;
                $count_grades = 0;
                $texts = [];
                $minuses = [];
                foreach($es_grades as $es_grade) {
                    $count_grades++;
                    $grade += (int)$es_grade->grade;
                    if($es_grade->text && strlen(trim($es_grade->text)) > 2) $texts[] = $es_grade->text;
                    if($es_grade->minus && strlen(trim($es_grade->minus)) > 2) $minuses[] = $es_grade->minus;
                }

                $avg = $count_grades > 0 ? round($grade / $count_grades, 1) : '';
                $arr[$i] = $avg;
                $arr['texts'][$i] = $texts;
                $arr['minuses'][$i] = $minuses;
            }

            $users[] = $arr;
        }

        $values_asc = array_column($users, 'group_id');
        array_multisort($values_asc, SORT_ASC, $users);

        return response()->json([
            'users' => $users
        ]);

    }



    public function estimate_your_trainer(Request $request)
    {

        if ($request->isMethod('get')) {
            $user = Auth::user();
            $groups = $user->inGroups();

            if(count($groups) == 0) {
                return redirect('/');
            }

            return view('estimate_your_trainer')->with([
                'rooks' => $this->getRooks($groups, 45), // руководители,
                'stars' => $this->getRooks($groups, 55), // старшие специалисты
            ]);
        }



        if ($request->isMethod('post')) {
            $user = Auth::user();

            $prev_month = Carbon::now()->subDays(28)->day(1)->format('Y-m-d');

            foreach($request->rooks as $rook) {
                if($rook['grade'] != 0) {
                    // Rukovoditel
                    $est = EstimateGrade::where('date', $prev_month)
                        ->where('user_id', $user->id)
                        ->where('about_id', $rook['id'])
                        ->first();

                    if($est) {
                        $est->grade = $rook['grade'];
                        $est->text = $rook['plus'];
                        $est->minus = $rook['minus'];
                        $est->save();
                    } else {
                        EstimateGrade::create([
                            'group_id' => 0,
                            'user_id' => $user->id,
                            'about_id' => $rook['id'],
                            'grade' => $rook['grade'],
                            'text' => $rook['plus'],
                            'minus' => $rook['minus'],
                            'date' => $prev_month,
                        ]);
                    }
                }
            }

            foreach($request->stars as $rook) {
                if($rook['grade'] != 0) {
                    // Rukovoditel
                    $est = EstimateGrade::where('date', $prev_month)
                        ->where('user_id', $user->id)
                        ->where('about_id', $rook['id'])
                        ->first();

                    if($est) {
                        $est->grade = $rook['grade'];
                        $est->text = $rook['plus'];
                        $est->minus = $rook['minus'];
                        $est->save();
                    } else {
                        EstimateGrade::create([
                            'group_id' => 0,
                            'user_id' => $user->id,
                            'about_id' => $rook['id'],
                            'grade' => $rook['grade'],
                            'text' => $rook['plus'],
                            'minus' => $rook['minus'],
                            'date' => $prev_month,
                        ]);
                    }
                }
            }

            return redirect('/');
        }


    }

    private function getRooks($groups, $position_id) {

        $user_ids = [];
        $users = [];

        foreach($groups as $group) {
            $group_users = ProfileGroup::employees($group->id);
            $_users = \DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereIn('users.id', $group_users)
                ->where('position_id', $position_id) // Руководитель, старший специалист группы
                ->get(['users.id', 'users.name', 'users.last_name']);

            foreach($_users as $user) {
                if(!in_array($user->id, $user_ids)) {
                    array_push($user_ids, $user->id);
                    array_push($users, [
                        'id' => $user->id,
                        'name' => $user->last_name . ' '. $user->name
                    ]);
                }

            }
        }

        if(count($users) == 0) {
            array_push($users, [
                'id' => 0,
                'name' => 'Без имени'
            ]);
        }

        return $users;
    }

}

