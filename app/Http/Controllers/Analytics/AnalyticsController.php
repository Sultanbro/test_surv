<?php

namespace App\Http\Controllers\Analytics;

use App\CallBase;
use App\Classes\Analytics\DM;
use App\Events\UserStatUpdatedEvent;
use App\Facade\Analytics\Analytics;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\Statistics\UpdateUserStatRequest;
use App\Imports\AnalyticsImport;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue;
use App\Models\Analytics\UserStat;
use App\Models\AnalyticsActivitiesSetting;
use App\ProfileGroup;
use App\Service\AnalyticService;
use App\Service\Department\UserService;
use App\Timetracking;
use App\TimetrackingHistory;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use View;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        View::share('title', 'Аналитика групп');
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->can('analytics_view')) {
            return redirect('/');
        }

        $groups = ProfileGroup::whereIn('has_analytics', [0, 1]);

        $_groups = [];

        if (auth()->user() && auth()->user()->is_admin == 1) $groups->whereIn('id', auth()->user()->groups);

        $groups = $groups->where('active', 1)->get();

        if (auth()->user()->is_admin != 1) {
            foreach ($groups as $key => $group) {
                $editors_id = $group->editors_id == null ? [] : json_decode($group->editors_id);
                if ($editors_id == null) continue;
                if (!in_array(auth()->id(), $editors_id)) continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        }

        View::share('menu', 'timetrackinganalytics');
        return view('admin.analytics-page', compact('groups'));
    }

    public function get(Request $request)
    {
        $group_id = $request->group_id;
        $month = $request->month;
        $year = $request->year;
        $date = Carbon::createFromDate($year, $month, 1);
        $currentUser = auth()->user();

        $group = ProfileGroup::find($group_id);
        if (!$group) {
            return [
                'error' => 'access',
            ];
        }

        if ($currentUser->is_admin != 1) {
            $editors_id = json_decode($group->editors_id);
            $group_editors = is_array($editors_id) ? $editors_id : [];
            if (!in_array($currentUser->id, $group_editors)) {
                return [
                    'error' => 'access',
                ];
            }
        }

        $groups = ProfileGroup::whereIn('has_analytics', [0, 1])->where('active', 1)->get();

        if (auth()->user()->is_admin != 1) {
            $_groups = [];
            foreach ($groups as $key => $group) {
                $editors_id = json_decode($group->editors_id);
                if ($editors_id == null) $editors_id = [];
                if (!in_array(auth()->id(), $editors_id)) continue;
                $_groups[] = $group;
            }
            $groups = $_groups;
        }

        $ac = AnalyticColumn::where('group_id', $group_id)->first();
        $ar = AnalyticRow::where('group_id', $group_id)->first();
        if (!$ac || !$ar) return [
            'error' => 'No analytics',
            'archived_groups' => ProfileGroup::where('has_analytics', -1)->where('active', 1)->get(),
            'groups' => $groups,
        ];

        // utility and rentability
        $util = TopValue::getUtilityGauges($date->format('Y-m-d'), [$group_id]);
        $rent = TopValue::getRentabilityGaugesOfGroup($date->format('Y-m-d'), $group_id, 'Рентабельность');
        if (count($util) > 0) {
            $util[0]['gauges'] = array_merge($util[0]['gauges'], $rent);
        }

        $call_bases = [];
        if ($group_id == 53) {
            $call_bases = CallBase::formTable($date->format('Y-m-d'));
        }

        $analyticService = new AnalyticService();
        $fired_percent_prev = $analyticService->getFiredUsersPerMonthPercent($group, $date->subMonth());
        $fired_percent = $analyticService->getFiredUsersPerMonthPercent($group, $date->addMonth());
        $fired_number_prev = $analyticService->getFiredUsersPerMonth($group, $date->subMonth());
        $fired_number = $analyticService->getFiredUsersPerMonth($group, $date->addMonth());

        return [
            'decomposition' => DecompositionValue::table($group_id, $date->format('Y-m-d')),
            'activities' => UserStat::activities($group_id, $date->format('Y-m-d')),
            'table' => AnalyticStat::form($group_id, $date->format('Y-m-d')),
            'columns' => AnalyticStat::columns($group_id, $date->format('Y-m-d')),
            'utility' => $util,
            'totals' => [],
            'groups' => $groups,
            'archived_groups' => ProfileGroup::where('has_analytics', -1)->get(),
            'call_bases' => $call_bases,
            'fired_percent_prev' => $fired_percent_prev,
            'fired_percent' => $fired_percent,
            'fired_number_prev' => $fired_number_prev,
            'fired_number' => $fired_number,
        ];
    }

    /**
     * saveCellActivity
     */
    public function saveCellActivity(Request $request)
    {
        $start = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        $columns = AnalyticColumn::where('date', $start)
            ->where('group_id', $request->group_id)
            ->whereNotIn('name', ['name', 'sum', 'avg', 'plan'])
            ->get();


        foreach ($columns as $key => $column) {
            $date = Carbon::createFromDate($request->year, $request->month, $column->name)->format('Y-m-d');

            $stat = AnalyticStat::where('date', $start)
                ->where('row_id', $request->row_id)
                ->where('column_id', $column->id)
                ->first();

            if ($stat) {
                $total_for_day = UserStat::total_for_day($request->activity_id, $date);

                $total_for_day = floor($total_for_day * 10) / 10;

                $stat->value = $total_for_day;
                $stat->show_value = $total_for_day;
                $stat->type = 'stat';
                $stat->class = $request->class;
                $stat->activity_id = $request->activity_id;
                $stat->save();
            }
        }
    }

    /**
     * saveCellTime
     */
    public function saveCellTime(Request $request)
    {
        $start = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        $columns = AnalyticColumn::query()
            ->where('date', $start)
            ->where('group_id', $request->group_id)
            ->whereNotIn('name', ['name', 'sum', 'avg', 'plan'])
            ->get();

        foreach ($columns as $key => $column) {
            $date = Carbon::createFromDate($request->year, $request->month, $column->name)->format('Y-m-d');

            $stat = AnalyticStat::query()
                ->where('date', $start)
                ->where('row_id', $request->row_id)
                ->where('column_id', $column->id)
                ->first();

            if ($stat) {
                $total_for_day = Timetracking::totalHours($date, $request->group_id);
                $total_for_day = floor($total_for_day / 9 * 10) / 10;

                $stat->value = $total_for_day;
                $stat->show_value = $total_for_day;
                $stat->type = 'time';
                $stat->class = $request->class;
                $stat->save();
            }
        }
    }

    /**
     * saveCellSum
     */
    public function saveCellSum(Request $request)
    {
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        $stat = AnalyticStat::query()
            ->where('date', $date)
            ->where('row_id', $request->row_id)
            ->where('column_id', $request->column_id)
            ->first();

        $total_for_day = 0;
        if ($stat) {
            $total_for_day = AnalyticStat::daysSum($date, $request->row_id, $request->group_id);

            $stat->value = $total_for_day;
            $stat->show_value = $total_for_day;
            $stat->type = 'sum';
            $stat->class = $request->class;
            $stat->save();
        }

        return $total_for_day;
    }

    /**
     * saveCellAvg
     */
    public function saveCellAvg(Request $request)
    {
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');

        $stat = AnalyticStat::query()
            ->where('date', $date)
            ->where('row_id', $request->row_id)
            ->where('column_id', $request->column_id)
            ->first();

        $total_for_day = 0;
        if ($stat) {
            $total_for_day = AnalyticStat::daysAvg($date, $request->row_id, $request->group_id);

            $stat->value = $total_for_day;
            $stat->show_value = $total_for_day;
            $stat->type = 'avg';
            $stat->class = $request->class;
            $stat->save();
        }

        return $total_for_day;
    }

    /**
     * Add row to group
     */
    public function addRow(Request $request)
    {
        $date = $request->date;
        $group_id = $request->group_id;

        return AnalyticStat::new_row($group_id, $request->after_row_id, $date);
    }

    /**
     * Remove dependency
     */
    public function removeDependency(Request $request)
    {
        $row = AnalyticRow::find($request->id);

        if ($row) {
            $row->depend_id = null;
            $row->save();
        }
    }

    /**
     * Delete row from group
     */
    public function deleteRow(Request $request)
    {
        $date = $request->date;
        $group_id = $request->group_id;
        $item = $request->item;

        $row_id = $item['name']['row_id'];
        $row = AnalyticRow::find($row_id);
        $row->delete();
        return 'Deleted';
    }

    /**
     * Edit stat
     */
    public function editStat(Request $request): JsonResponse
    {

        /** @var AnalyticStat $stat */
        $stat = AnalyticStat::query()
            ->where('date', $request->get("date"))
            ->where('row_id', $request->get("row_id"))
            ->where('column_id', $request->get("column_id"))
            ->first();

        if (!$stat) return $this->response(
            message: 'Statistic not found!',
            data: [],
            code: ResponseAlias::HTTP_NOT_FOUND,
        );

        $old_value = $stat->value;
        $stat->value = $request->get("value");
        $stat->show_value = $request->get("show_value");

        if ($request->get("type") == 'formula') {
            /** @var Analytics $service */
            $service = app(Analytics::class);
            $stat->value = $service->convertCellFormulaToCoordinates($stat, $request->get("value"), $request->get("formula"));
        }

        if ($request->get("type") == 'remote' || $request->get("type") == 'inhouse') {

            $type = $request->get("type") == 'remote' ? 'remote' : 'office';
            /** @var AnalyticColumn $column */
            $column = AnalyticColumn::withTrashed()->find($request->get("column_id"));

            if ($column) {
                $date = Carbon::parse($request->get("date"))
                    ->day($column->name)
                    ->format('Y-m-d');

                $this->addHours($request->get("group_id"), $type, $request->get("value"), $old_value, $date);
            }

            $stat->comment = $request->get("comment");
        }

        $stat->type = $request->get("type");
        $stat->class = $request->get("class");
        $stat->save();

        return $this->response(
            message: 'Success!',
            data: $stat->toArray(),
            code: ResponseAlias::HTTP_ACCEPTED,
        );
    }

    public function addHours($group_id, $user_type, $value, $old_value, $date)
    {
        // TODO users
        $group_users = collect((new UserService)->getEmployees($group_id, $date))->pluck('id')->toArray();

        if (tenant('id') === 'bp') {
            $user_ids = User::withTrashed()
                ->where('position_id', 32) // operators only
                ->whereIn('id', $group_users)
                ->get(['id'])
                ->pluck('id')
                ->toArray();
        }

        $tts = Timetracking::whereIn('user_id', $user_ids)
            ->whereDate('enter', $date)
            ->orderBy('enter', 'desc')
            ->get();

        $marked_users = [];

        foreach ($tts as $tt) {
            $user = User::withTrashed()->find($tt->user_id);
            if (!$user) continue;
            if (!$user->user_type) continue;
            if ($user->user_type != $user_type) continue;

            if (!in_array($tt->user_id, $marked_users)) {
                $old_value = is_numeric($old_value) ? (int)$old_value : 0;
                $new_value = $tt->total_hours + $value - $old_value;
                if ($new_value < 0) $new_value = 0;
                $tt->total_hours = $new_value;

                $tt->updated = 1;
                $tt->save();

                array_push($marked_users, $tt->user_id);

                if ($value == 0) {
                    $desc = 'Отмена: Минуты за "Отсутствие связи"';
                } else {
                    $old_text = $old_value != 0 ? ', минус предыдущие добавленные ' . $old_value . ' минут' : '';
                    $desc = 'Отсутствие связи. <br> Добавлено ' . $value . ' минут ' . $old_text;
                }

                TimetrackingHistory::create([
                    'user_id' => $tt->user_id,
                    'author_id' => auth()->id(),
                    'author' => auth()->user()->last_name . ' ' . auth()->user()->name,
                    'date' => $date,
                    'description' => $desc,
                ]);
            }
        }
    }

    /**
     * New group analytics
     */
    public function newGroup(Request $request)
    {
        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        AnalyticColumn::defaults($request->group_id, $date);
        AnalyticRow::defaults($request->group_id, $date);

        $group = ProfileGroup::find($request->group_id);
        if ($group) {
            $group->has_analytics = 1;
            $group->save();
        }

        Activity::createQuality($request->group_id);
    }

    /**
     * Create new activity for group in Analytics page
     */
    public function createActivity(Request $request)
    {

        $act = Activity::create([
            'name' => $request->activity['name'],
            'group_id' => $request->group_id,
            'daily_plan' => $request->activity['daily_plan'],
            'plan_unit' => $request->activity['plan_unit'],
            'unit' => $request->activity['unit'],
            'weekdays' => $request->activity['weekdays'],
            'ud_ves' => 0,
            'source' => Activity::SOURCE_GROUP
        ]);

        $date = Carbon::createFromDate($request->year, $request->month, 1)->format('Y-m-d');
        return UserStat::activities($request->group_id, $date);
    }

    /**
     * Edit activity in group
     */
    public function editActivity(Request $request)
    {
        $act = Activity::find($request->activity['id']);
        if ($act) {
            (new AnalyticService)->updatePlanPerMonth(
                $act,
                $request->activity['daily_plan'],
                $request->activity['plan_unit'],
                $request->year,
                $request->month,
            );
            $act->update([
                'name' => $request->activity['name'],
                'daily_plan' => $request->activity['daily_plan'],
                'plan_unit' => $request->activity['plan_unit'],
                'unit' => $request->activity['unit'],
                'weekdays' => $request->activity['weekdays'],
            ]);
        }
    }

    /**
     * Edit User Stat value
     */
    public function updateUserStat(UpdateUserStatRequest $request)
    {
        $dto = $request->toDto();
        $group = ProfileGroup::find($dto->groupId);
        $date = Carbon::createFromDate($dto->year, $dto->month, $dto->day)->format('Y-m-d');

        $us = UserStat::where('date', $date)
            ->where('user_id', $dto->employeeId)
            ->where('activity_id', $dto->activityId)
            ->first();

        if ($us) {
            $us->value = $dto->value;
            $us->save();
        } else {
            UserStat::create([
                'date' => $date,
                'user_id' => $dto->employeeId,
                'activity_id' => $dto->activityId,
                'value' => $dto->value,
            ]);
        }
        UserStatUpdatedEvent::dispatch($dto);

        if ($group->time_address == $dto->activityId && !in_array($dto->employeeId, $group->time_exceptions)) {
            Timetracking::updateTimes($dto->employeeId, $date, $dto->value * 60);
        }

        if (tenant('id') != 'bp') {
            return null;
        }

        if ($dto->groupId == 31 && $dto->activityId == 20) { // DM and 20 колво действий
            DM::updateTimesNew($dto->employeeId, $date);
        }

        if ($dto->groupId == 31 && $dto->activityId == 21) {
            DM::updateTimesByWorkHours($dto->employeeId, $date, $dto->day, (float)$dto->value);
        }
    }

    /**
     * Change order of activities in group
     */
    public function change_order(Request $request)
    {
        $order = count($request->activities);
        foreach ($request->activities as $activity) {
            $act = Activity::find($activity['id']);
            $act->order = $order--;
            $act->save();
        }
    }

    /**
     * Delete activity
     */
    public function delete_activity(Request $request)
    {
        $act = Activity::find($request['id']);
        $act->delete();
    }

    /**
     * Set decimals to cell
     */
    public function setDecimals(Request $request)
    {
        $stat = AnalyticStat::where('column_id', $request->column_id)
            ->where('row_id', $request->row_id)
            ->first();

        if ($stat) {
            $stat->decimals = $request->decimals;
            $stat->save();
        }
    }

    /**
     * Add comparing row
     */
    public function add_depend(Request $request)
    {
        $row = AnalyticRow::find($request->id);
        if ($row) {
            $row->depend_id = $request->depend_id;
            $row->save();
        }
    }

    /**
     * Archive analytics for group
     */
    public function archive_analytics(Request $request)
    {
        $group = ProfileGroup::find($request->id);
        if ($group) {
            $group->has_analytics = -1;
            $group->archived_date = Carbon::now()->toDateString();
            $group->save();
        }
    }

    /**
     * Restore analytics for group from archive
     */
    public function restore_analytics(Request $request)
    {
        $group = ProfileGroup::find($request->id);
        if ($group) {
            $group->has_analytics = 1;
            $group->archived_date = null;
            $group->save();
        }
    }

    /**
     * Add remote or inhouse row to сводная
     */
    public function addRemoteInhouse(Request $request)
    {
        $date = $request->date;
        $formula_row = AnalyticRow::find($request->row_id);

        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        $columns = AnalyticColumn::where('group_id', $formula_row->group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        foreach ($columns as $column) {
            $stat = AnalyticStat::query()
                ->where('row_id', $formula_row->id)
                ->where('column_id', $column->id)
                ->first();
            $fields = [
                'group_id' => $formula_row->group_id,
                'date' => $date,
                'row_id' => $formula_row->id,
                'column_id' => $column->id,
                'value' => 0,
                'show_value' => 0,
                'type' => $request->type,
                'class' => 'text-center',
                'editable' => 1,
            ];

            if ($stat) {
                $stat->update($fields);
            } else {
                AnalyticStat::create($fields);
            }
        }

    }

    /**
     * add salary row to сводная
     */
    public function addSalary(Request $request)
    {
        $date = $request->date;
        $type = 'salary_day'; // no comment please. Just salary is not free

        $formula_row = AnalyticRow::find($request->row_id);

        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        $columns = AnalyticColumn::query()
            ->where('group_id', $formula_row->group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        foreach ($columns as $column) {
            $stat = AnalyticStat::query()
                ->where('row_id', $formula_row->id)
                ->where('column_id', $column->id)
                ->first();

            $fields = [
                'group_id' => $formula_row->group_id,
                'date' => $date,
                'row_id' => $formula_row->id,
                'column_id' => $column->id,
                'value' => 0,
                'show_value' => 0,
                'type' => $type,
                'class' => 'text-center',
                'editable' => 1,
            ];

            if ($stat) {
                $stat->update($fields);
            } else {
                AnalyticStat::create($fields);
            }
        }
    }

    /**
     * Add formula to row to сводная
     */
    public function addFormula_1_31(Request $request)
    {
        $date = $request->date;

        $formula_row = AnalyticRow::find($request->row_id);
        $rows = AnalyticRow::where('group_id', $formula_row->group_id)->get();

        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];

        $columns = AnalyticColumn::query()
            ->where('group_id', $formula_row->group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        foreach ($columns as $column) {
            $stat = AnalyticStat::query()
                ->where('row_id', $formula_row->id)
                ->where('column_id', $column->id)
                ->first();

            $formula = $request->formula;
            foreach ($rows as $key => $row) {
                $formula = str_replace("{" . $row->id . "}", "[" . $column->id . ":" . $row->id . "]", $formula);
            }

            // replace text
            $pattern = '/[a-zA-Z]+[0-9]+/i';
            $formula = preg_replace($pattern, 1, $formula);

            $fields = [
                'group_id' => $formula_row->group_id,
                'date' => $date,
                'row_id' => $formula_row->id,
                'column_id' => $column->id,
                'value' => $formula,
                'show_value' => 0,
                'type' => 'formula',
                'class' => 'text-center',
                'decimals' => $request->decimals,
                'editable' => 1,
            ];

            //save update service
            if ($stat) {
                $stat->update($fields);
            } else {
                AnalyticStat::create($fields);
            }
        }

    }


    /**
     * Экспорт активностей (Подробная аналитика) группы в Excel
     */
    public function exportActivityExcel(Request $request)
    {

        $group = ProfileGroup::find($request->group_id);

        $request->month = (int)$request->month;
        $currentUser = auth()->user();

        $editors_id = json_decode($group->editors_id);
        $group_editors = is_array($editors_id) ? $editors_id : [];
        // Доступ к группе


        if (!auth()->user()->can('analytics_view') && !in_array($currentUser->id, $group_editors)) {
            return [
                'error' => 'access',
            ];
        }

        // $group_accounts = DB::connection('callibro')->table('call_account')
        //         ->where('owner_uid', 5)->whereIn('email', $users_emails)
        //         ->groupBy('call_account.id')
        //         ->orderBy('full_name')
        //         ->get(['id', 'email', 'name', 'surname', DB::raw("CONCAT(surname,' ',name) as full_name")]);

        $date = Carbon::createFromDate($request->year, $request->month, 1);
        $userIds = (new UserService)->getEmployeeIds($group->id, $date->format('Y-m-d'));

        $this->users = User::withTrashed()->whereIn('id', $userIds)
            ->get(['ID as id', 'email as email', 'name as name', 'last_name as surname', DB::raw("CONCAT(last_name,' ',name) as full_name")]);;

        /****************************** */
        /******==================================== */


        $title = 'Аналитика активностей ' . $request->month . ' месяц ' . $request->year;

        $data = UserStat::activities($request->group_id, $date->format('Y-m-d'));

        /******==================================== */

        $sheets = [];

        $minute_headings = Activity::getHeadings($date, Activity::UNIT_MINUTES);
        $percent_headings = Activity::getHeadings($date, Activity::UNIT_PERCENTS);

        foreach ($data as $sheet_content) {
            $sheets[] = [
                'title' => $sheet_content['name'],
                'headings' => $minute_headings,
                'sheet' => Activity::getSheet($sheet_content['records'], $date, Activity::UNIT_MINUTES)
            ];
        }


        /******==================================== */

        if (ob_get_length() > 0) ob_clean(); //  ob_end_clean();

        if ($date->daysInMonth == 28) $last_cell = 'AH3';
        if ($date->daysInMonth == 29) $last_cell = 'AI3';
        if ($date->daysInMonth == 30) $last_cell = 'AJ3';
        if ($date->daysInMonth == 31) $last_cell = 'AK3';

        return Excel::download(new AnalyticsImport($sheets, $group), $title . ' "' . $group->name . '".xls');

    }

    /**
     * Request:
     *  {
     *    "year": 2022,
     *    "group_id": 31
     *  }
     *
     * Response:
     * {
     *    "status": 200,
     *    "message": "success",
     *    "data": {
     *      "1": [
     *        {
     *          "total": 2495,
     *          "user_id": 2658,
     *          "users": {
     *              {
     *                 "id": 2658,
     *                 "name": "Vasya",
     *                 "last_name" "Pupkin"
     *              }
     *        }...
     *    }
     * }
     *
     * @param Request $request
     * @return mixed
     */
    public function getUserStatisticsByMonth(Request $request)
    {
        $date = $request->input('date') ?? null;
        $groupId = $request->input('group_id') ?? null;
        $activity_id = $request->input('activity_id') ?? null;

        $response = (new AnalyticService)->userStatisticsPerMonth($date, $groupId, $activity_id);

        return response()->success($response);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function removeActivityGroup(Request $request)
    {
        $groupId = $request->group_id;
        $groups = $request->groups;
        $tableName = (new AnalyticsActivitiesSetting())->getTable();

        $data = [];
        foreach ($groups as $key => $value) {
            $data[AnalyticsActivitiesSetting::COLUMN_PREFIX . $key] = json_encode($value);
        }
        $columns = array_keys($data);
        $data['group_id'] = $groupId;

        foreach ($columns as $column) {
            if (!Schema::hasColumn($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column) {
                    $table->text($column)->nullable()->comment("activities.id && activities.name");
                });
            }
        }

        $analyticSetting = AnalyticsActivitiesSetting::updateOrCreate(['group_id' => $groupId], $data);
        return response()->success($analyticSetting);
    }

    public function getRemovedUsers($id)
    {
        $removedUsers = AnalyticsActivitiesSetting::where('group_id', $id)->first();
        return response()->success($removedUsers);
    }
}


