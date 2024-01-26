<?php

namespace App\Http\Controllers\Analytics;

use App\Classes\Analytics\Recruiting;
use App\Http\Controllers\Controller;
use App\Models\Analytics\Activity;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\CustomProceed;
use App\Models\Analytics\TopEditedValue;
use App\Models\Analytics\TopValue;
use App\Models\Analytics\UserStat;
use App\ProfileGroup;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use View;

class TopController extends Controller
{
    /**
     * Id групп
     */
    public array $groups;

    public function __construct()
    {
        View::share('title', 'ТОП');
        $this->middleware('auth');
    }

    /**
     * Страница аналитика группы
     */
    public function index()
    {
        View::share('menu', 'timetrackingtop');
        /** @var User $user */
        $user = auth()->user();

        if (!$user->can('top_view')) {
            return redirect('/');
        }

        $date = Carbon::now()->startOfMOnth()->format('Y-m-d');

        return view('admin.top')->with([
            'data' => [
                'rentability' => TopValue::getRentabilityGauges($date),
                'utility' => TopValue::getUtilityGauges($date),
                'proceeds' => $this->getProceeds($date),
                'prognoz_groups' => Recruiting::getPrognozGroups($date),
            ],
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $date = Carbon::createFromDate(
              year: $request->get("year")
            , month: $request->get("month")
            , day: 1)
            ->format('Y-m-d');

        return response()->json([
            'rentability' => TopValue::getRentabilityGauges($date),
            'utility' => TopValue::getUtilityGauges($date),
            'proceeds' => $this->getProceeds($date),
        ]);

    }

    /**
     * Сводная таблица рентабельности за год
     * @throws Exception
     */
    public function getRentability(Request $request): array
    {
        return TopValue::getPivotRentability(
              $request->get("year")
            , $request->get("month")
        );
    }

    /**
     * Сводная таблица рентабельности только за месяц
     * @throws Exception
     */
    public function getRentabilityOnMonth(Request $request): array
    {
        return TopValue::getPivotRentabilityOnMonth(
            $request->get("year")
            , $request->get("month"));
    }

    /**
     * Таблица выручки колл центра
     */
    private function getProceeds($date): array
    {
        $calendar_days = Carbon::parse($date)->daysInMonth; // календарные дни

        $days = $this->daysInMonth($date);
        // get weeks array
        $weeks = [];

        $start_week = 1;
        if($days[0]->dayOfWeek == 1) $start_week = 0;

        foreach($days as $key => $date) {
            if($date->dayOfWeek == '1') {
                $start_week++;
            }
            $weeks[$start_week][] = $date;
        }

        $week_proceeds = [
            'fields' => $this->getProceedFields($days),
            'records' => [],
        ];

        $total_row = [];
        $total_row['Отдел'] = 'Итого';
        $total_row['План'] = 0;
        $total_row['Итого'] = 0;

        foreach($weeks as $key => $week) {
            $total_row['w'.$key] = 0;
        }

        foreach($days as $date) {
            $total_row[$date->format('d.m')] = 0;
        }

        $this->groups = ProfileGroup::profileGroupsWithArchived($date->year, $date->month, true, true, ProfileGroup::SWITCH_PROCEEDS);

        foreach($this->groups as $group_id) {
            $group = ProfileGroup::find($group_id);
            $row = [];

            $firstDayMonth = Carbon::create($date->year, $date->month)->firstOfMonth()->format('Y-m-d');
            if ($group->archived_date && $group->archived_date >= $firstDayMonth) {
                $firstDayNextMonth = Carbon::create($date->year, $date->month + 1)->firstOfMonth()->format('Y-m-d');
                $row["deleted_at"] = $firstDayNextMonth;
            }

            if($group) {

                $row['Отдел'] = $group->name;
                $row['group_id'] = $group->id;

                $sum = 0;
                $filled_days = 0;

                $prs = AnalyticStat::getProceeds($group_id, $date);
                $plan = AnalyticStat::getProceedsPlan($group_id, $date);

                $row['%'] = 2;
                $row['План'] = $plan;
                $row['+/-'] = 2;
                $total_row['План'] += $plan;



                foreach($weeks as $key => $week) {
                    $xsum = 0;
                    foreach($week as $date) {
                        $row[$date->format('d.m')] = (int)$prs[$date->day];
                        if((int)$prs[$date->day] > 0) $filled_days++;
                        $sum += $prs[$date->day];
                        $total_row[$date->format('d.m')] += $prs[$date->day];
                        $xsum += $prs[$date->day];
                    }
                    $row['w'. ($key)] = round($xsum);
                    $total_row['w'. $key] += round($xsum);
                }


                $row['Итого'] = (int)$sum;
                $total_row['Итого'] += $sum;

                $row['+/-'] = '';
                $total_row['+/-'] = '';

                $row['%'] = '';
                $total_row['%'] = '';

                if($filled_days > 0 && $plan > 0) {
                    $row['+/-'] = (int)$sum / ($plan / $calendar_days * $filled_days) * 100;
                    $row['+/-'] = (round($row['+/-'],1) - 100) . '%';

                    $row['%'] = ((int)$sum * 100) / $plan;
                    $row['%'] = round($row['%'],1) . '%';
                }



                array_push($week_proceeds['records'], $row);
            }

        }

        $total_row['План'] = (int)$total_row['План'];
        $total_row['Итого'] = (int)$total_row['Итого'];


        $cps = CustomProceed::whereYear('date', $days[0]->year)
            ->whereMonth('date', $days[0]->month)
            ->get()
            ->groupBy('order');

        foreach ($cps as $order => $cpo) {
            // editable row with inputs
            $editable_row = [];
            $editable_row['Отдел'] = $cpo->first() ? $cpo->first()->name : '';
            $editable_row['План'] = 0;
            $editable_row['Итого'] = 0;

            foreach($weeks as $key => $week) {
                $sum = 0;
                foreach($week as $date) {
                    $d = $cpo->where('date', $date->format('Y-m-d'))->first();

                    $val = $d ? (int)$d->value : 0;
                    $sum += $val;
                    $editable_row[$date->format('d.m')] = $val;
                    $editable_row['Итого'] += $val;
                    $total_row[$date->format('d.m')] += $val;
                }

                $editable_row['group_id'] =  0 - $order;
                $editable_row['w'. ($key)] = $sum;

            }

            array_push($week_proceeds['records'], $editable_row);


            $total_row['Итого'] += $editable_row['Итого'];
        }

        foreach($days as $date) {
            $total_row[$date->format('d.m')] = (int)number_format($total_row[$date->format('d.m')], 0);
        }

        array_push($week_proceeds['records'], $total_row);

        return $week_proceeds;
    }

    private function getProceedFields($days): array
    {
        $fields = [];
        $fields[] = 'Отдел';
        $fields[] = '%';
        $fields[] = 'План';
        $fields[] = 'Итого';
        $fields[] = '+/-';

        // get weeks
        $start_week = 1;
        $last_week = null;

        foreach ($days as $key => $date) {

            // define first week field
            if (
                $date->dayOfWeek == '1'
                || ($key == 0)
            ) {
                if ($last_week != null) {
                    $fields[] = $last_week;
                }
                $last_week = 'w' . $start_week;
                $start_week++;
            }

            // push day
            $fields[] = $date->format('d.m');

            // push last week field because we didn't reach monday
            if (count($days) - 1 == $key && $date->dayOfWeek != '1') {
                $fields[] = $last_week;
            }
        }

        return $fields;
    }

    /**
     * Даты для таблицы выручки
     */
    private function daysInMonth($date): array
    {
        $date = Carbon::parse($date)->startOfMonth();

        $days = [];

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $days[] = Carbon::parse($date->day($i)->format('Y-m-d'));
        }

        return $days;
    }

    public function saveRentMax(Request $request): Response
    {
        $gauge = $request->get("gauge");

        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()
            ->find($gauge['group_id']);

        $group->rentability_max = $gauge['max_value'];
        $group->save();
        return response()->noContent(200);
    }

    public function saveGroupPlan(Request $request): Response
    {
        /** @var ProfileGroup $group */
        $group = ProfileGroup::query()
            ->find($request->get("group_id"));

        $group->required = $request->get("plan");
        $group->save();
        return response()->noContent(200);
    }

    public function saveTopValue(Request $request): array
    {

        $gauge = $request->get("gauge");

        /** @var TopValue $top_value */
        $top_value = TopValue::query()
            ->find($gauge['id']);

        if ($top_value) {

            $top_value->value = $gauge['value'];
            $top_value->name = $gauge['name'];
            $top_value->value_type = $gauge['value_type'];
            $top_value->round = $gauge['round'];
            $top_value->is_main = $gauge['is_main'];
//
//            if ($gauge['is_main'] == 1) {
//                $tvs = TopValue::query()
//                    ->where('group_id', $top_value->group_id)
//                    ->where('date', $top_value->date)
//                    ->update(['is_main' => 0]);
//            }
            $top_value->min_value = $gauge['min_value'];
            $top_value->cell = $gauge['cell'];
            $top_value->activity_id = $gauge['activity_id'];
            $top_value->max_value = $gauge['max_value'];
            $top_value->reversed = $gauge['reversed'];
            $top_value->options = json_encode($gauge['options']);
            $top_value->unit = $gauge['unit'] ?: '';
            $top_value->save();

            $value = $top_value->value;
            if ($top_value->activity_id != 0 && $top_value->activity_id != -1) {
                $value = UserStat::total_for_month($top_value->activity_id, $top_value->date, $top_value->value_type);
            }

            $options = TopValue::getDynamicValue($top_value->group_id, $top_value->date, $top_value)['options'];


            return ['code' => 200, 'value' => $value, 'options' => $options];
        } else {
            return ['code' => 404];
        }

    }

    public function getActivities(Request $request): Collection|array
    {
        return Activity::query()
            ->where('group_id', $request->get("group_id"))
            ->get();
    }

    public function createGauge(Request $request): array
    {
        $group_id = $request->get("group_id");
        $activity_id = $request->get("activity_id");

        $top_value = new TopValue();

        $top_value->value_type = $request->get("value_type");
        $top_value->round = $request->get("round");
        $top_value->is_main = $request->get("is_main");
        $top_value->min_value = $request->get("min_value");
        $top_value->max_value = $request->get("max_value");
        $top_value->reversed = $request->get("reversed");

        $top_value->activity_id = $activity_id;
        $top_value->name = $request->get("name");
        $top_value->group_id = $group_id;
        $top_value->date = Carbon::now()->day(1)->format('Y-m-d');
        $top_value->cell = $request->get("cell");
        $top_value->value_type = $request->get("value_type");
        $top_value->unit = $request->get("unit");
        $top_value->options = json_encode($request->get("options"));
        $top_value->save();

        return [
            'id' => $top_value->getKey(),
            'name' => $top_value->name,
            'value' => $top_value->value,
            'group_id' => $top_value->group_id,
            'place' => 1,
            'unit' => $top_value->unit,
            'editable' => false,
            'edit_value' => true,
            'key' => $top_value->getKey() * 1000,
            'min_value' => $top_value->min_value,
            'activity_id' => $top_value->activity_id,
            'max_value' => $top_value->max_value,
            'cell' => $top_value->cell,
            'sections' => json_encode($request->get("options")["staticLabels"]["labels"]),
            'options' => json_decode($top_value->options),
            'diff' => 0
        ];
    }

    public function deleteGauge(Request $request): Response
    {
        TopValue::query()
            ->find($request->all()['gauge']['id']);

        return response()->noContent(200);
    }

    public function updateTopEditedValue(Request $request): Response
    {
        $date = Carbon::createFromDate($request
            ->get("year"), $request->get("month"), 1)
            ->format('Y-m-d');

        /** @var TopEditedValue $tev */
        $tev = TopEditedValue::query()
            ->where('group_id', $request->get("group_id"))
            ->where('date', $date)
            ->first();

        if ($tev) {
            if ($request->get("value")) {
                $tev->value = $request->get("value");
                $tev->save();
            } else {
                $tev->delete();
            }

        } else {
            TopEditedValue::query()
                ->create([
                    'date' => $date,
                    'group_id' => $request->get("group_id"),
                    'value' => $request->get("value"),
                ]);
        }
        return response()->noContent(200);
    }

    public function updateProceeds(Request $request): Response
    {
        $date = explode('.', $request->get('date'));

        $day = (int)$date[0];
        $month = (int)$date[1];
        $year = $request->get("year");

        $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');

        /** @var CustomProceed $cp */
        $cp = CustomProceed::query()
            ->where('date', $date)
            ->where('order', abs($request->get('group_id')))
            ->first();

        if ($cp) {
            $cp->value = $request->get('value');
            $cp->name = $request->get('name');
            $cp->save();
        } else {
            CustomProceed::query()
                ->create([
                    'date' => $date,
                    'name' => $request->get('name'),
                    'order' => abs($request->get('group_id')),
                    'value' => $request->get('value'),
                ]);
        }

        $cps = CustomProceed::query()
            ->where('date', $date)
            ->where('order', abs($request->get('group_id')))
            ->get();

        foreach ($cps as $cp) {
            $cp->name = $request->get('name');
            $cp->save();
        }
        return response()->noContent(200);
    }
}

