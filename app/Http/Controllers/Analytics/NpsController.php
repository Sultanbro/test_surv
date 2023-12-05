<?php

namespace App\Http\Controllers\Analytics;

use DB;
use Illuminate\Http\JsonResponse;
use View;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\ProfileGroup;
use App\EstimateGrade;
use App\Position;

class NpsController extends Controller
{
    const USER_HEAD = 0;
    const USER_SPEC = 1;

    public function __construct()
    {
        View::share('title', 'NPS');
        $this->middleware('auth');
    }

    /**
     * Страница NPS в ТОП
     * @method POST
     */
    public function fetch(Request $request): JsonResponse
    {
        $users = [];

        $_users = DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            ->whereIn('position_id', [45,55]) // Руководитель, старший специалист группы
            ->get(['users.id', 'users.name', 'users.last_name', 'users.position_id']);

        foreach($_users as $user) {
            /** @var User $user */
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
                $m = Carbon::createFromDate($request['year'], $i, 1)->format('Y-m-d');
                $es_grades = EstimateGrade::query()->where('date', $m)
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
            return $this->estimate_your_trainer_get();
        }

        if ($request->isMethod('post')) {
            return $this->estimate_your_trainer_post($request);
        }

        return false;
    }

    public function estimate_your_trainer_get(){
        $user = Auth::user();
        $groups = $user->inGroups();

        if(count($groups) == 0) {
            return redirect('/');
        }

        return view('specific.estimate_your_trainer')->with([
            'rooks' => $this->getRooksV2($groups, self::USER_HEAD), // руководители,
            'stars' => $this->getRooksV2($groups, self::USER_SPEC), // старшие специалисты
        ]);
    }

    public function estimate_your_trainer_post(Request $request){
        $user = Auth::user();
        $prev_month = Carbon::now()->subDays(28)->day(1)->format('Y-m-d');

        $this->saveGrades($request->rooks, $user, $prev_month);
        $this->saveGrades($request->stars, $user, $prev_month);
        return redirect('/');
    }

    public function saveGrades($grades, $user, $date){
        foreach($grades as $grade) {
            if($grade['grade'] != 0) {
                $est = EstimateGrade::query()->where('date', $date)
                    ->where('user_id', $user->id)
                    ->where('about_id', $grade['id'])
                    ->first();

                if($est) {
                    $est->grade = $grade['grade'];
                    $est->text = $grade['plus'];
                    $est->minus = $grade['minus'];
                    $est->save();
                } else {
                    EstimateGrade::create([
                        'group_id' => 0,
                        'user_id' => $user->id,
                        'about_id' => $grade['id'],
                        'grade' => $grade['grade'],
                        'text' => $grade['plus'],
                        'minus' => $grade['minus'],
                        'date' => $date,
                    ]);
                }
            }
        }
    }

    /**
     * Получить руководителей или старших спецов
     */
    private function getRooksV2($groups, $userType): array
    {
        $field = $userType == self::USER_SPEC ? 'is_spec' : 'is_head';

        $user_ids = [];
        $users = [];

        $positions = Position::query()->select(['id'])
            ->where($field, 1)
            ->get()
            ->pluck('id');



        foreach($groups as $group) {
            $group_users = ProfileGroup::employees($group->id);
            $_users = DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereIn('users.id', $group_users)
                ->whereIn('position_id', $positions)
                ->get(['users.id', 'users.name', 'users.last_name']);

            foreach($_users as $user) {
                if(!in_array($user->id, $user_ids)) {
                    $user_ids[] = $user->id;
                    $users[] = [
                        'id' => $user->id,
                        'name' => $user->last_name . ' ' . $user->name,
                        'type' => $userType,
                    ];
                }
            }
        }

        if(count($users) == 0) {
            $users[] = [
                'id' => 0,
                'name' => 'Без имени',
                'type' => $userType,
            ];
        }
        return $users;
    }

    /**
     * Получить руководителей или старших спецов
     */
    private function getRooks($groups, $position_id): array
    {

        $user_ids = [];
        $users = [];

        foreach($groups as $group) {
            $group_users = ProfileGroup::employees($group->id);
            $_users = DB::table('users')
                ->whereNull('deleted_at')
                ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
                ->where('is_trainee', 0)
                ->whereIn('users.id', $group_users)
                ->where('position_id', $position_id) // Руководитель, старший специалист группы
                ->get(['users.id', 'users.name', 'users.last_name']);

            foreach($_users as $user) {
                if(!in_array($user->id, $user_ids)) {
                    $user_ids[] = $user->id;
                    $users[] = [
                        'id' => $user->id,
                        'name' => $user->last_name . ' ' . $user->name
                    ];
                }

            }
        }

        if(count($users) == 0) {
            $users[] = [
                'id' => 0,
                'name' => 'Без имени'
            ];
        }

        return $users;
    }

}

