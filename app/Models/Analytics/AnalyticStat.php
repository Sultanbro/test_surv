<?php

namespace App\Models\Analytics;

use App\Facade\Analytics\Analytics;
use App\GroupSalary;
use App\Models\Analytics\AnalyticColumn as Column;
use App\Models\Analytics\AnalyticRow as Row;
use App\ProfileGroup;
use App\Repositories\Analytics\AnalyticStatRepository;
use App\Timetracking;
use Carbon\Carbon;
use DivisionByZeroError;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

/**
 * @property  int $row_id
 * @property  int $column_id
 * @property  string $date
 * @property  int $group_id
 * @property  string $value
 * @property  string $show_value
 * @property  int $activity_id
 * @property  string $type
 * @property  string $editable
 * @property  string $class
 * @property  string $decimals
 * @property  string $comment
 * relations
 * @property Activity $activity
 * @property AnalyticRow $analyticRow
 * @property AnalyticColumn $analyticColumn
 */

/**
 * @property string $value
 * @property string $show_value
 */
class AnalyticStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'row_id',
        'column_id',
        'date',
        'group_id',
        'value',
        'show_value',
        'activity_id',
        'type',
        'editable',
        'class',
        'decimals',
        'comment'
    ];

    const INITIAL = 'initial'; // text field
    const FORMULA = 'formula'; // calculating field
    const TIME = 'time'; // hour from timetracking
    const STAT = 'stat'; // value from individual stat
    const AVG = 'avg'; // avg from individual stat for 31 days
    const SUM = 'sum'; // sum from individual stat for 31 days
    const SALARY = 'salary'; // sum of salaries
    const REMOTE = 'remote'; // additional remote minutes
    const INHOUSE = 'inhouse'; // additional inhouse minutes
    const SHOW_VALUE_INHOUSE = "Отсутствие связи: in house";

    public function column(): BelongsTo
    {
        return $this->belongsTo(
            AnalyticColumn::class,
            'column_id',
            'id'
        );
    }

    public function row(): BelongsTo
    {
        return $this->belongsTo(
            AnalyticRow::class,
            'row_id',
            'id'
        );
    }

    /**
     * Form pivot table to vue
     */
    public static function form($group_id, $date): array
    {
        $statRepository = app(AnalyticStatRepository::class);
        $stats = $statRepository->getByGroupId($groupId, $date);

        $table = [];

        $rows = Row::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->orderBy('order', 'desc') // это неправильно правильно с order
            ->get();

        $columns = Column::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->orderBy('order', 'asc')
            ->get();

        $weekdays = self::getWeekdays($date); // coloring weekdays

        $row_keys = [];
        $col_keys = [];

        // for formula cell determinations
        foreach ($rows as $r_index => $row) {
            $row_keys[$row->id] = $r_index + 1;
            foreach ($columns as $c_index => $column) {
                $col_keys[$column->id] = $c_index != 0 ? self::getLetter($c_index - 1) : 'A';
            }
        }

        $all_activities = Activity::withTrashed()->where('group_id', $group_id)->get();
        $all_stats = self::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->get();

        // returning items
        foreach ($rows as $r_index => $row) {

            $item = [];

            $cell_number = $r_index + 1;

            $depending_from_row = Row::query()
                ->where('depend_id', $row->id)
                ->first();

            foreach ($columns as $c_index => $column) {
                $add_class = Analytics::getClass($column->name, $weekdays, $depending_from_row);


                $l = $c_index != 0 ? self::getLetter($c_index - 1) : 'A';
                $cell_letter = $l;

                $stat = $all_stats->where('row_id', $row->id)
                    ->where('column_id', $column->id)
                    ->first();

                if ($stat) { // if exist

                    $arr = Analytics::getArr($stat, $row, $column, $cell_letter, $cell_number, $add_class, $r_index);

                    if ($stat->activity_id != null) {
                        $act = $all_activities->where('id', $stat->activity_id)->first();
                        if ($act && $act->unit) {
                            $arr['sign'] = $act->unit;
                        }
                    }
                    if ($stat->type == 'formula') {
                        $arr['value'] = self::convert_formula($stat->value, $row_keys, $col_keys);
                        $val = self::calcFormula(
                            stat: $stat,
                            date: $date,
                            round: $stat->decimals,
                            stats: $stats
                        );

                        $arr['show_value'] = $val;
                        $stat->show_value = $val;
                        $stat->save();
                    }


                    if ($stat->type == 'stat') {

                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');
                        $val = UserStat::total_for_day($stat->activity_id, $day);
                        $stat->show_value = $val;
                        $stat->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($stat->type == 'sum') {
                        $val = self::daysSum($date, $row->id, $group_id);
                        $val = round($val, 1);
                        $stat->show_value = $val;
                        $stat->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($stat->type == 'avg') {
                        $val = self::daysAvg($date, $row->id, $group_id);
                        $stat->show_value = round($val, 1);
                        $stat->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($stat->type == 'salary') {
                        $gsalary = GroupSalary::query()
                            ->where('group_id', $group_id)
                            ->where('date', $date)
                            ->get()
                            ->sum('total');
                        $val = floor($gsalary);
                        $stat->show_value = $val;
                        $stat->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($stat->type == 'salary_day' && !in_array($column->name, ['plan', 'sum', 'avg', 'name'])) {
                        $gsalary = 0;

                        $val = floor($gsalary);
                        $stat->show_value = $val;
                        $stat->save();
                        $arr['value'] = $val;
                        $arr['show_value'] = $val;
                    }

                    if ($stat->type == 'time') {

                        $column = AnalyticColumn::query()->find($column->id);
                        $day = Carbon::parse($date)->day($column->name)->format('Y-m-d');

                        $val = Timetracking::totalHours($day, $group_id);
                        $val = floor($val / 9 * 10) / 10;
                        //$val = $val - 1;
                        if ($val < 0) $val = 0;

                        $stat->show_value = $val;
                        $stat->save();

                        $arr['value'] = round($val, 1);
                        $arr['show_value'] = round($val, 1);
                    }


                } else { //if not exist then create

                    $type = 'initial';
                    if ($column->name == 'sum' && $r_index > 3) $type = 'sum';
                    if ($column->name == 'avg' && $r_index > 3) $type = 'avg';
                    self::query()->create([
                        'group_id' => $group_id,
                        'date' => $date,
                        'row_id' => $row->id,
                        'column_id' => $column->id,
                        'value' => '',
                        'show_value' => '',
                        'decimals' => 0,
                        'type' => $type,
                        'class' => 'text-center' . $add_class,
                        // 'editable' => (($r_index == 2 || $r_index == 3) && !in_array($column->name, ['plan', 'sum','name'])) || $r_index == 0 ? 0 : 1,
                        'editable' => $r_index == 0 ? 0 : 1,
                    ]);

                    $arr = [
                        'value' => '',
                        'show_value' => '',
                        'context' => false,
                        'row_id' => $row->id,
                        'column_id' => $column->id,
                        'decimals' => 0,
                        'type' => $type,
                        'cell' => $cell_letter . $cell_number,
                        'class' => 'text-center' . $add_class,
                        //'editable' => (($r_index == 2 || $r_index == 3) && !in_array($column->name, ['plan', 'sum','name'])) || $r_index == 0 ? 0 : 1,
                        'editable' => $r_index == 0 ? 0 : 1,
                        'depend_id' => $row->depend_id,
                        'comment' => '',
                        'sign' => '',
                    ];
                }

                $item[$column->name] = $arr;
            }

            $table[] = $item;
        }


        foreach ($table as $row) {

            foreach ($row as $key => $item) {
                if ($item['type'] == 'sum') {

                    $val = self::daysSum($date, $item['row_id'], $group_id);
                    $val = floor($val);

                    $row[$key]['value'] = $val;
                    $row[$key]['show_value'] = $val;

                    $stat = self::query()
                        ->where('column_id', $item['column_id'])
                        ->where('row_id', $item['row_id'])
                        ->first();

                    if ($stat) {
                        $stat->show_value = $val;
                        $stat->value = $val;
                        $stat->save();
                    }
                    //memp($val);
                    // TODO sum days save to stat
                }

                if ($item['type'] == 'avg') {
                    $val = self::daysAvg($date, $item['row_id'], $group_id);

                    $row[$key]['value'] = $val;
                    $row[$key]['show_value'] = $val;

                    $stat = self::query()
                        ->where('column_id', $item['column_id'])
                        ->where('row_id', $item['row_id'])
                        ->first();

                    if ($stat) {
                        $stat->show_value = $val;
                        $stat->value = $val;
                        $stat->save();
                    }
                    // TODO avg days save to stat
                }
            }
        }

        return $table;
    }

    /**
     * Columns array for pivot table
     */
    public static function getColumns($group_id, $date): array
    {
        $columns = [];

        $_columns = Column::where('group_id', $group_id)
            ->where('date', $date)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($_columns as $key => $column) {
            $columns[] = [
                'key' => $column->name,
            ];
        }

        return $columns;
    }

    public static function new_row($group_id, $after_row_id, $date): array
    {

        $new_row = Row::create([
            'group_id' => $group_id,
            'date' => $date,
            'name' => 'name',
            'order' => 1,
        ]);

        $after_row_order = Row::find($after_row_id)->order;

        $rows = Row::where('group_id', $group_id)
            ->where('date', $date)
            ->where('order', '>=', $after_row_order)
            ->orderBy('order', 'asc')
            ->get();
        $new_row->order = $after_row_order++;
        $new_row->save();

        foreach ($rows as $key => $row) {
            $row->order = $after_row_order++;
            $row->save();
        }

        $columns = Column::where('group_id', $group_id)
            ->where('date', $date)
            ->get();

        $item = [];

        foreach ($columns as $column) {


            $type = 'initial';
            if ($column->name == 'sum') $type = 'sum';
            if ($column->name == 'avg') $type = 'avg';

            $class = 'text-center';
            if ($column->name == 'name') $class = 'text-left';

            $stat = self::create([
                'group_id' => $group_id,
                'date' => $date,
                'row_id' => $new_row->id,
                'column_id' => $column->id,
                'value' => '',
                'show_value' => '',
                'editable' => 1,
                'type' => $type,
                'class' => $class
            ]);

            $arr = [
                'value' => '',
                'show_value' => '',
                'row_id' => $new_row->id,
                'column_id' => $column->id,
                'context' => false,
                'type' => $type,
                'class' => $class,
            ];


            $item[$column->name] = $arr;
        }

        return $item;

    }


    /**
     * convert cells
     * from [123:34] to E5
     *
     */
    public static function convert_formula($text, $row_keys, $col_keys)
    {
        $matches = [];
        preg_match_all('/\[{1}\d+:\d+\]{1}/', $text, $matches);
        foreach ($matches[0] as $match) {
            $match = str_replace(["[", "]"], "", $match);
            $exp = explode(':', $match);
            if (array_key_exists($exp[0], $col_keys) && array_key_exists($exp[1], $row_keys)) {
                $text = str_replace("[" . $match . "]", self::getLetter($col_keys[$exp[0]]) . $row_keys[$exp[1]], $text);
            } else {
                $text = str_replace("[" . $match . "]", '0', $text);
            }
        }
        return $text;
    }

    /**
     * convert cells
     * from [123:34] to [234:45]
     */
    public static function convert_formula_to_new_month($text, $row_keys, $col_keys)
    {
        $matches = [];
        preg_match_all('/\[{1}\d+:\d+\]{1}/', $text, $matches);

        foreach ($matches[0] as $match) {
            $match = str_replace("[", "", $match);
            $match = str_replace("]", "", $match);
            $exp = explode(':', $match);
            if (array_key_exists($exp[0], $col_keys) && array_key_exists($exp[1], $row_keys)) {
                $text = str_replace("[" . $match . "]", "[" . $col_keys[$exp[0]] . ':' . $row_keys[$exp[1]] . "]", $text);
            } else {
                $text = str_replace("[" . $match . "]", '0', $text);
            }
        }

        return $text;
    }

    public static function getLetter($number): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];


        $sl_pos = -1;

        $fl_pos = ($number + 1) % 26;
        if ($fl_pos == 0) $sl_pos++;

        if ($number >= 26 && $fl_pos != 0) {
            $sl_pos = 0;
        }

        if ($sl_pos >= 0) {
            $res = $letters[$sl_pos] . $letters[$fl_pos];
        } else {
            $res = $letters[$fl_pos];
        }


        return $res;
    }

    public static function daysAvg($date, $row_id, $group_id): float|int
    {
        $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        $columns = AnalyticColumn::where('group_id', $group_id)->where('date', $date)->whereIn('name', $days)->get();

        $total = 0;
        $count = 0;

        $all_stats = self::where('row_id', $row_id)->where('date', $date)->get();

        foreach ($columns as $key => $column) {
            $stat = $all_stats->where('column_id', $column->id)->first();

            if ($stat) {
                $total += (float)$stat->show_value;
                //if($row_id == 1837) dump($stat->show_value);
                if ((float)$stat->show_value != 0) {

                    $count++;
                }
            }
        }

        // if($row_id == 1837) dd($total, $count);
        if ($count > 0) {
            $total = round($total / $count, 3);
        } else {
            $total = 0;
        }

        return $total;
    }

    public static function daysSum($date, $row_id, $group_id, $days = []): float|int
    {

        if (count($days) == 0) {
            $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        }

        $columns = AnalyticColumn::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        $total = 0;

        $all_stats = self::query()
            ->where('row_id', $row_id)
            ->where('date', $date)
            ->get();

        foreach ($columns as $column) {
            $stat = $all_stats->where('column_id', $column->id)->first();

            if ($stat and is_numeric($stat->show_value)) {
                $total += (float)$stat->show_value;
            }
        }

        return $total;
    }


    public static function getWeekdays($date): array
    {
        $arr = [];

        $date = Carbon::parse($date);

        for ($i = 1; $i <= $date->daysInMonth; $i++) {
            $day = $date->day($i)->isWeekday();
            if ($day) {
                $arr[] = $i;
            }
        }

        return $arr;
    }

    public static function calcFormula(AnalyticStat $stat, string $date, int $round = 1, array $only_days = [], Collection $stats = null): float|int
    {
        $text = $stat->value;

        $matches = [];
        preg_match_all('/\[{1}\d+:\d+\]{1}/', $text, $matches);

        foreach ($matches[0] as $match) {
            $match = str_replace(["[", "]"], "", $match);
            $exp = explode(':', $match);
            $column_id = $exp[0];
            $row_id = $exp[1];

            /** @var AnalyticStat $cell */
            $cell = ($stats ?? AnalyticStat::query())
                ->where('column_id', $column_id)
                ->where('row_id', $row_id)
                ->where('date', $date)
                ->first();

            if ($cell) {
                if ($cell->type == 'formula') {
                    $sameStat = $cell->row_id == $stat->row_id && $cell->column_id == $stat->column_id;
                    if ($sameStat) continue;
                    $value = self::calcFormula($cell, $date, 10, $only_days, $stats);
                    $text = str_replace("[" . $match . "]", (float)$value, $text);
                } else if ($cell->type == 'sum') {
                    //dump($only_days);
                    $value = self::daysSum($date, $cell->row_id, $cell->group_id, $only_days);
                    //dump('sum ' .$value);
                    $text = str_replace("[" . $match . "]", (float)$value, $text);
                } else {
                    // dump('value ' . $cell->show_value);
                    $text = str_replace("[" . $match . "]", (float)$cell->show_value, $text);
                }
            }
        }


        try {
            $text = str_replace(",", ".", $text);
            $text = str_replace("=", "", $text);


            if ($text == '') $text = '0';

            //$math_string ="return (".$text.");";
            $math_string = "return " . $text . ";";

            if (str_contains($math_string, '{')) {
                $math_string = str_replace("{", "", $math_string);
                $math_string = str_replace("}", "", $math_string);
            }
            $math_string = str_replace("%", "", $math_string);

            $res = eval($math_string);
        } catch (DivisionByZeroError|Throwable) {
            $res = 0;
        }

        return round($res, $round);
    }

    public static function getRentability(ProfileGroup $group, $date): float|int
    {
        $date = Carbon::parse($date)
            ->day(1)
            ->format('Y-m-d');
        $column = Column::where('group_id', $group->id)
            ->where('date', $date)
            ->where('name', 'plan')
            ->first();

        $row = Row::where('group_id', $group->id)
            ->where('date', $date)
            ->where('name', 'Impl')
            ->first();

        $val = 0;
        if ($row && $column) {
            $stat = self::where('column_id', $column->id)
                ->where('row_id', $row->id)
                ->where('date', $date)
                ->first();
            if ($stat) {
                $val = self::calcFormula($stat, $date, 2);
                $stat->show_value = $val;
                $stat->save();
            }
        }


        return $val;
    }

    public static function getProceedsPlan($group_id, $date): float|int
    {
        $date = Carbon::parse($date)->day(1)->format('Y-m-d');
        $column = Column::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'sum')
            ->first();

        $row = Row::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'Pr, cstll')
            ->first();

        $val = 0;

        if ($row && $column) {
            $stat = self::where('column_id', $column->id)
                ->where('row_id', $row->id)
                ->where('date', $date)
                ->first();

            if ($stat && $stat->type == 'formula') {
                $val = self::calcFormula($stat, $date, 2);
            }
        }


        return $val;
    }

    public static function getProceeds($group_id, $date, $only_days = [])
    {
        $date = Carbon::parse($date)->day(1)->format('Y-m-d');

        if (count($only_days) > 0) {
            $days = $only_days;
        } else {
            $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        }

        $columns = Column::where('group_id', $group_id)
            ->where('date', $date)
            ->whereIn('name', $days)
            ->get();

        $row = Row::where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'second')
            ->first();

        $values = [];
        for ($i = 1; $i <= Carbon::parse($date)->daysInMonth; $i++) {
            $values[$i] = 0;
        }

        if ($row) {
            foreach ($columns as $column) {
                $stat = self::where('column_id', $column->id)
                    ->where('row_id', $row->id)
                    ->where('date', $date)
                    ->first();

                if ($stat) {

                    if ($stat->type == 'formula') {
                        $values[(int)$column->name] = self::calcFormula($stat, $date);
                    } else {
                        $values[(int)$column->name] = (int)$stat->show_value;
                    }


                }
            }

        }

        return $values;
    }

    public static function getProceedsSum($group_id, $date)
    {
        $values = self::getProceeds($group_id, $date);
        $sum = 0;
        foreach ($values as $value) {
            $sum += $value;
        }
        return $sum;
    }

    /**
     * get value from cell string
     * ex: C4 or A5
     */
    public static function getCellValue($group_id, $cell, $date, $round = 0): float|int
    {
        // get indexes
        $r_index = 0;
        $c_index = 0;

        $column_letters = '';
        $row_letters = '';

        $matches = [];
        preg_match_all('/[a-zA-Z]{1,2}+/', $cell, $matches);

        if (count($matches[0]) > 0) {
            $column_letters = strtoupper($matches[0][0]);
        }

        $matches = [];
        preg_match_all('/\d+/', $cell, $matches);

        if (count($matches[0]) > 0) {
            $row_letters = $matches[0][0];
        }
        if ($column_letters != '') {
            $i = 0;
            if ($column_letters == 'A') {
                $c_index = 0;
            } else {
                while ($column_letters != self::getLetter($i)) {
                    $i++;
                }
                $c_index = $i + 1;
            }
        }

        if ($row_letters != '') {
            $r_index = (int)$row_letters - 1;
        }

        $columns = AnalyticColumn::query()
            ->where('date', $date)
            ->where('group_id', $group_id)
            ->orderBy('order')
            ->get();

        $column = $columns->toArray()[$c_index] ?? null;
        // get row

        $rows = AnalyticRow::query()
            ->where('date', $date)
            ->where('group_id', $group_id)
            ->orderBy('order', 'desc')
            ->get();

        $row = $rows->toArray()[$r_index] ?? null;
        // get cell

        $value = 0;
        if ($row && $column) {
            $stat = self::query()->where('date', $date)
                ->where('column_id', $column['id'])
                ->where('row_id', $row['id'])
                ->first();

            if ($stat) {
                if ($stat->type == 'formula') {
                    $value = self::calcFormula($stat, $date, $round);
                } else {
                    $value = round((float)$stat->show_value, $round);
                }
            }
        }

        return $value;
    }


    /**
     * Получить рентабельность на конкретный день в месяце
     */
    public static function getRentabilityOnDay(int $group_id, string $date): float|int
    {
        $impl = 0;
        $only_days = [];

        $day = Carbon::parse($date)->day;
        for ($i = 1; $i <= $day; $i++) {
            $only_days[] = $i;
        }

        $date = Carbon::parse($date)->day(1)->format('Y-m-d');
        $stat = self::getImplStat($group_id, $date);

        if ($stat) {
            $impl = self::calcFormula($stat, $date, 2, $only_days);
        }

        return $impl;
    }

    public static function getImplStat(int $group_id, string $date): null|AnalyticStat|Model
    {
        $column = Column::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'plan')
            ->first();

        $row = Row::query()
            ->where('group_id', $group_id)
            ->where('date', $date)
            ->where('name', 'Impl')
            ->first();

        return $column && $row ? self::query()
            ->where('column_id', $column->getKey())
            ->where('row_id', $row->getKey())
            ->where('date', $date)
            ->first() : null;
    }

    public static function getRentabilityDiff(int $group_id, string $date): float
    {
        $impl = self::getRentabilityOnDay($group_id, $date);

        $prev_date = Carbon::parse($date)->subMonth()->format('Y-m-d');
        $impl_prev = self::getRentabilityOnDay($group_id, $prev_date);

        return round($impl - $impl_prev, 2);
    }

    public static function inHouseShowValue(int $groupId, Carbon $date): static|null
    {
        /** @var AnalyticStat */
        return self::query()
            ->where("group_id", $groupId)
            ->where("show_value", self::SHOW_VALUE_INHOUSE)
            ->where('date', $date->format('Y-m-d'))
            ->first();
    }

    public static function getValuesWithRow(AnalyticStat $analyticStat): Collection|array
    {
        return self::query()
            ->where('group_id', $analyticStat->group_id)
            ->where('date', $analyticStat->date)
            ->where('row_id', $analyticStat->row_id)
            ->where('type', self::INHOUSE)
            ->where('value', '!=', '')
            ->whereNotNull('value')
            ->get();
    }

    public static function getProceedsSumForListOfGroups(array $groups, Carbon $date, array $only_days = []): array
    {
        $values = [];

        if (count($only_days) > 0) {
            $days = $only_days;
        } else {
            $days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31];
        }

        $stats = self::query()
            ->withWhereHas('column', fn($query) => $query->whereIn('name', $days))
            ->withWhereHas('row', fn($query) => $query->where('name', 'second'))
            ->whereDate('date', $date)
            ->whereIn('group_id', $groups)
            ->get()
            ->keyBy('group_id');

        foreach ($stats as $groupId => $stat) {
            $sum = 0;
            if ($stat->type == 'formula') {
                $sum += self::calcFormula($stat, $date);
            } else {
                $sum += (int)$stat->show_value;
            }
            $values[$groupId] = $sum;
        }

        foreach ($groups as $group) {
            if (!array_key_exists($group, $values)) $values[$group] = 0;
        }

        return $values;
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}