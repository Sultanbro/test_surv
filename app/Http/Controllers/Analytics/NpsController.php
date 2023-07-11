<?php

namespace App\Http\Controllers\Analytics;

use DB;
use View;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ProfileGroup;
use App\EstimateGrade;

class NpsController extends Controller
{

    public function __construct()
    {
        View::share('title', 'NPS');
        $this->middleware('auth');
    }

    /**
     * Страница NPS в ТОП
     * @method POST
     */
    public function fetch(Request $request)
    {



        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');



        $users = [];

        $_users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('position_id', [45,55]) // Руководитель, старший специалист группы
            ->get(['users.id', 'users.name', 'users.last_name', 'users.position_id']);

        foreach($_users as $user) {

            $user = User::withTrashed()->find($user->id);
            $groups = $user->position_id == 45 ? $user->inGroups(true) : $user->inGroups();
            $group = count($groups) > 0 ? $groups[0]->name : '.Без группы';


            $arr = [
                'id' => $user->id,
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

                $arr['grades'][$i] = $count_grades; // quantity for every month
                $arr['texts'][$i] = $texts; // pluses
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


    /**
     * Оценка тренера
     */
    public function estimate_your_trainer(Request $request)
    {

        if ($request->isMethod('get')) {
            $user = Auth::user();
            $groups = $user->inGroups();

            if(count($groups) == 0) {
                return redirect('/');
            }

            return view('specific.estimate_your_trainer')->with([
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

    /**
     * Получить руководителей или старших спецов
     */
    private function getRooks($groups, $position_id)
    {

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

