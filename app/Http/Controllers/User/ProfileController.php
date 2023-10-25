<?php

namespace App\Http\Controllers\User;

use App\Classes\Analytics\Recruiting as RM;
use App\Classes\Helpers\Currency;
use App\Downloads;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Admin\ObtainedBonus;
use App\Models\Analytics\Activity;
use App\Models\Analytics\RecruiterStat;
use App\Models\Analytics\TraineeReport;
use App\Models\Analytics\UserStat;
use App\Models\GroupUser;
use App\Models\Kpi\Bonus;
use App\Photo;
use App\User;
use App\Position;
use App\PositionDescription;
use App\ProfileGroup;
use App\QualityRecordWeeklyStat;
use App\Service\Admin\UserService as AdminUserService;
use App\Service\Courses\CourseResultService;
use App\Service\Department\UserService;
use App\UserExperience;
use App\Zarplata;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Класс отвечает за профиль пользователя.
 */
class ProfileController extends Controller
{
    public $user;
    public AdminUserService $userService;

    public function __construct(AdminUserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
        $this->user        = auth()->user();
    }

    public function newprofile()
    {
        // admin.jobtron.org
        if(request()->getHost() === 'admin.' .config('app.domain')) {
            return view('admin');
        }

        return view('newprofile');
    }

    public function profile(UserProfileUpdateRequest $request): bool
    {
        if (isset($request->email))
        {
            $this->userService->updateEmail($request);
        }

        if (isset($request->currency))
        {
            $this->userService->updateCurrency($request);
        }

        if (isset($request->password))
        {
            $this->userService->changePassword($request);
        }

        return true;
    }

    /**
     * @return JsonResponse
     */
    public function getBonuses(Request $request) : JsonResponse
    {
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        $user = auth()->user() ? User::find(auth()->id()) : null;

        $currency_rate = $user && in_array($user->currency, array_keys(Currency::rates()))
            ? (float)Currency::rates()[$user->currency]
            : 0.0000001;

        return response()->success([
            'history' => ObtainedBonus::getHistoryJSON($user->id, $date, $currency_rate),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function traineeReport(): JsonResponse
    {
        $response = $this->userService->getTraineeReport();

        return response()->json($response);
    }

    /**
     * @return JsonResponse
     */
    public function recruterStatsRates(): JsonResponse
    {
        $recruiter_stats_rates = [];

        for ($i = 1; $i <= Carbon::now()->daysInMonth; $i++) {
            $rec = new RM();
            $value = $rec->getOnlineRates(Carbon::now()->day($i)->format('Y-m-d'));
            $recruiter_stats_rates[$i] = $value;
        }
        $recruiter_stats_rates = json_encode($recruiter_stats_rates);

        return response()->success($recruiter_stats_rates);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function personalInfo(Request $request): JsonResponse
    {;
        $response = $this->userService->getPersonalData();

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function activities(Request $request): JsonResponse
    {
        $response = $this->userService->getActivitiesToProfile($request);

        return response()->json($response);
    }

    /**
     * @return JsonResponse
     */
    public function courses(): JsonResponse
    {
        $user = auth()->user();

        return response()->json($user->getActiveCourses());
    }

    /**
     * Get course by id
     *
     * @return JsonResponse
     */
    public function course(Request $request, CourseResultService $service): JsonResponse
    {
        $response = $service->getCourseWithResults($request->id, auth()->id());

        return response()->json($response);
    }

    /**
     * old func
     */
    public function getProfile(Request $request)
    {
        $user = User::find(auth()->id());

        $currency_rate = in_array($user->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$user->currency] : 0.0000001;

        $positions = Position::all();
        $photo     = Photo::where('user_id', $user->id)->first();
        $downloads = Downloads::where('user_id', $user->id)->first();
        $user_position = Position::find($user->position_id);

        /*** Группы пользователя */
        $groups = '';
        $gs = $user->inGroups();

        foreach($gs as $group) {
            $groups .= '<div>' . $group['name'] . '</div>';
        }

        /*** Текущая книга для прочтения */
        $book = null;

        /* recruiter */


        $rg_users = [];
        if(tenant('id') == 'bp') {
            $rec_group = ProfileGroup::find(48);

            if($rec_group) {
                $working  = (new UserService)->getEmployees(48, date('Y-m-d'));
                $rg_users = collect($working)->pluck('id')->toArray();
            }

        }



        $recruiter_stats = json_encode([]);
        $recruiter_records = json_encode([]);

        // workdays recruiter

        $ignore = $user->working_day_id == 1 ? [6,0] : [0];
        $workdays = workdays(date('Y'), date('m'), $ignore);
        $wd = $user->workdays_from_applied(date('Y-m-d'), $user->working_day_id == 1 ? 5 : 6);
        if($wd != 0) $workdays = $wd;

        // another code

        if(in_array($user->id, $rg_users)) {
            $is_recruiter = true;
            $recruiter_stats = json_encode(RecruiterStat::tables(date('Y-m-d')));

            $asi  = null;

            if($asi) {
                $recruiter_records = json_decode($asi->data);
            }  else {
                $rm = new RM();
                $recruiter_records = $rm->defaultUserTable($user->id)['records'];
            }

            $indicators = $this->recruiting_temp();
        } else {
            $is_recruiter = false;
            $indicators = json_encode([]);
        }

        $head_in_groups = [];
        $trainee_report = [];


        /**
         * костыль Корп универ должен видеть эту таблицу
         * TraineeReport::getBlocks
         */

        $corpUni = tenant('id') == 'bp'
            ? GroupUser::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('group_id', 96)
                ->first()
            : null;

        /**
         * fetch TraineeReport::getBlocks
         * оценки руководителей
         */
        if($corpUni) {
            $head_in_groups = [1];
            $trainee_report = TraineeReport::getBlocks(date('Y-m-d'));

        }

        /**
         * checktime for trainees
         */
        // foreach($head_in_groups as $group) {
        //     if(Carbon::parse($group->checktime)->timestamp - time() >= 0) {
        //         $group->checktime = Carbon::parse($group->checktime)->setTimezone('Asia/Almaty');
        //     } else {
        //         $group->checktime = null;
        //     }
        // }

        // month for js

        $month = [
            'daysInMonth' => Carbon::now()->daysInMonth,
            'currentMonth' => Carbon::now()->format('F')
        ];

        /**
         * recruiter stats
         */
        $recruiter_stats_rates = [];

        for ($i = 1; $i <= Carbon::now()->daysInMonth; $i++) {
            $rec = new RM();
            $value = $rec->getOnlineRates(Carbon::now()->day($i)->format('Y-m-d'));
            $recruiter_stats_rates[$i] = $value;
        }
        $recruiter_stats_rates = json_encode($recruiter_stats_rates);

        /**
         * zarplata
         */
        $zarplata = Zarplata::where('user_id', $user->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;
        $oklad = round($oklad * $currency_rate, 0);
        $oklad = number_format($oklad, 0, '.', ' ');

        // arc
        $activities = '[]';
        $quality = [];

        if(count($gs) > 0) {
            $request->group_id = $gs[0]->id;
            $_activities = Activity::where('group_id', $gs[0]->id)->first();

            $activities = UserStat::activities($gs[0]->id , date('Y-m-d'));
            $activities = json_encode($activities);

            $users_ids = (new UserService)->getEmployees($gs[0]->id, date('Y-m-d'));

            $quality = $_activities ? QualityRecordWeeklyStat::table($users_ids, date('Y-m-d')) : [];

        }

        $show_payment_terms = false;
        foreach ($gs as $key => $gr) {
            if($gr->payment_terms && $gr->payment_terms != '' && $gr->show_payment_terms == 1) {
                $show_payment_terms = true;
            }
        }

        $blocks_number = 1;

        $position_desc = PositionDescription::where('position_id', $user->position_id)->first();
        if($position_desc && $position_desc->show == 1) $blocks_number++;
        if($show_payment_terms) $blocks_number++;


        /////////////////////////////////////
        //View::share('title', 'Мой профиль');
        View::share('menu', 'profile');

        return view('admin.timetracking', compact(
            'user',
            'oklad',
            'positions',
            'user_position',
            'photo',
            'downloads',
            'groups',
            'book',
            'is_recruiter',
            'indicators',
            'month',
            'recruiter_stats',
            'recruiter_stats_rates',
            'recruiter_records',
            'head_in_groups'
        ))->with([
            'answers' => UserExperience::getAnswers($user->id),
            'position_desc' => $position_desc,
            'groups_pt' => $gs,
            'show_payment_terms' => $show_payment_terms,
            'blocks_number' => $blocks_number,
            'activities' => $activities,
            'quality' => $quality,
            'trainee_report' => $trainee_report,
            'courses' => $user->getActiveCourses(),
            'workdays' => $workdays
        ]);
    }

    /**
     * Recruiting temp function
     * Only for BP
     *
     * @return String|false
     */
    private function recruiting_temp() : String|false
    {
        if(tenant('id') != 'bp') return json_encode([]);

        $group = ProfileGroup::find(48);

        $indicators = []; // Для визуальных данных под сводной таблицей

        // AGAIN USERS
        $trainees = (new UserService)->getEmployees($group->id, date('Y-m-d'));
        $user_ids = collect($trainees)->pluck('id')->toArray();

        $t = RM::getTableRecruiters($user_ids, ['year' => date('Y'), 'month' => date('m')]);

        $indicators['recruiters'] = $t['recruiters'];

        /// Заказы руководителей
        $orders = [];
        $orderGroups = ProfileGroup::where('active', 1)->get();
        foreach ($orderGroups as $group) {
            $orders[] = [
                'group'    => $group->name,
                'required' => $group->required,
                'fact'     => $group->provided . ' ',
            ];
        }

        $indicators['orders'] = $orders;

        // Count remain days
        $start = Carbon::now();
        $end = Carbon::now()->setDate(date('Y'), date('m'), 1)->endOfMonth();


        if($end->timestamp - $start->timestamp >= 0 && $end->month >= date('m')) {
            $remain_days = $start->diffInDaysFiltered(function (Carbon $date) use ($holidays) {
                return !$date->isDayOfWeek(Carbon::SUNDAY); //&& !in_array($date, $holidays);
            }, $end);
        } else {
            $remain_days = 0;
        }

        $indicators['info']['remain_days'] = $remain_days;

        return json_encode($indicators);
    }


    /**
     * Условия оплаты из отделов и должности
     *
     * @return JsonResponse
     */
    public function paymentTerms(Request $request): JsonResponse
    {
        $response = $this->userService->paymentTerms($request);

        return response()->json($response);
    }

    /**
     * Условия оплаты из отделов и должности
     *
     * @return
     */
    public function promotionalMaterial()
    {
        return view('promotional-material');
    }

    public function updateWelcomeMessage(Request $request): JsonResponse
    {
        $ownerId = auth()->id(); // validated by middleware guard
        $owner = User::getAuthUser($ownerId);
        $request->validate([
            'welcome_message' => 'required|string'
        ]);

        $owner->welcome_message = $request->get('welcome_message');
        $owner->update();

        return response()->json([
            'user' => $owner
        ]);
    }
}
