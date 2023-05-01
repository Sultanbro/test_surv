<?php

namespace App\Console\Commands\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreatePivotAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:pivots';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create AnalyticStats for group';

    /**
     * Create a new command instance.
     *
     * @return void 
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->line('start creating pivot tables:');
        
//        if(Carbon::now()->day != 1) return false;

        $newDate  = Carbon::now()->day(1)->format('Y-m-d');
        $prevDate = Carbon::now()->subMonth()->day(1)->format('Y-m-d');

        $groups   = ProfileGroup::query()
                    ->where('active', 1)
                    ->where('has_analytics', 1)
                    ->get();

        foreach ($groups as $group) {

            $this->line('group ' . $group->id . ' ' . $group->name);

            $newRows = $this->createRows($group->id, $prevDate, $newDate);
            $newCols = $this->createCols($group->id, $prevDate, $newDate);

            $this->createStats($group->id, $prevDate, $newDate, $newRows, $newCols);
            
        }

        $this->line('end');
    }

    /**
     * Create rows
     * 
     * @param int $group_id
     * @param String $prevDate
     * @param String $newDate
     * @return array
     */
    private function createRows(int $group_id, String $prevDate, String $newDate) : array
    {
        $newRows = [];

        $rows = AnalyticRow::where('date', $prevDate)
                ->where('group_id', $group_id)
                ->orderBy('order','desc')
                ->get();

        foreach($rows as $row) {
            $newRow = AnalyticRow::create([
                'group_id'     => $row->group_id,
                'name'         => $row->name,
                'date'         => $newDate,
                'order'        => $row->order,
                'depend_id'    => $row->depend_id,
            ]);

            $newRows[$row->id] = $newRow->id;
        }

        /**
         * depend rows
         */
        $rows = AnalyticRow::where('date', $newDate)
            ->where('group_id', $group_id)
            ->whereNotNull('depend_id')
            ->orderBy('order','desc')
            ->get();

        foreach ($rows as $key => $row) {
            $row->depend_id = in_array($row->id, $newRows)
                            && array_key_exists($row->depend_id, $newRows)
                            ? $newRows[$row->depend_id]
                            : null;
            $row->save();
        }

        return $newRows;
    }

    /**
     * Create cols
     * 
     * @param int $group_id
     * @param String $prevDate
     * @param String $newDate
     * @return array
     */
    private function createCols(int $group_id, String $prevDate, String $newDate) : array
    {
        $newColumns = [];

        /**
         * Дни в этом и в прошлом месяце.
         */
        $daysInMonth     = Carbon::parse($newDate)->daysInMonth; 
        $daysInPrevMonth = Carbon::parse($prevDate)->daysInMonth;

        /**
         * Получаем данные за прошлый месяц.
         */
        $cols = AnalyticColumn::query()->where([
            ['date', '=', $prevDate],
            ['group_id', '=', $group_id]
        ])->whereIn('name', $this->getNameColumn($newDate))->orderBy('order','asc')->get();


        $lastOrder = 0;
        $analyticColumns = [];

        foreach($cols as $col) {
            $analyticColumn = AnalyticColumn::query()->create([
                'group_id' => $col->group_id,
                'name'     => $col->name,
                'date'     => $newDate,
                'order'    => $col->order,
            ]);

            /**
             * Получаем последний элемент в массиве.
             */
            $lastOrder = $col->order;

            /**
             * Сохраняем ID новой колонки в массиве.
             */
            $newColumns[$col->id] = $analyticColumn->id;
        }

        /**
         * Скрипт запускается если дни текущего месяца больше чем прошлый.
         */

        if ($daysInMonth > $daysInPrevMonth)
        {
            $dayDiff = $daysInMonth - $daysInPrevMonth;
            $diffDays = array_diff($this->getCurrentMonthDayToArray($daysInMonth), $this->getPrevMonthDayToArray($daysInPrevMonth));

            foreach ($diffDays as $diffDay)
            {
                $analyticColumns[] = [
                    'group_id' => $group_id,
                    'name'     => (string) $diffDay,
                    'date'     => $newDate,
                    'order'    => ++$lastOrder,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            DB::table('analytic_columns')->insert($analyticColumns);
        }

        return $newColumns;
    }

    /**
     * @param $prevMonthDays
     * @return array
     */
    private function getPrevMonthDayToArray($prevMonthDays): array
    {
        $days = [];

        for ($day = 1; $day <= $prevMonthDays; $day++)
        {
            $days[] = $day;
        }

        return $days;
    }

    /**
     * @param $currentMonthDays
     * @return array
     */
    private function getCurrentMonthDayToArray($currentMonthDays): array
    {
        $days = [];

        for ($day = 1; $day <= $currentMonthDays; $day++)
        {
            $days[] = $day;
        }

        return $days;
    }

    private function getNameColumn(string $date): array
    {
        /**
         * Добавляем 4 потому что есть колонки name, plan, avg, sum и дни в месяце.
         */
        $nameColumn = ['name', 'plan', 'sum', 'avg'];
        $daysInMonth = Carbon::parse($date)->daysInMonth;

        for ($column = 1; $column <= $daysInMonth; $column++)
        {
            $nameColumn[] = $column;
        }

        return $nameColumn;
    }

    /**
     * day columnsMissingFromPreviousMonth
     * 
     * @param int $daysInPrevMonth
     * @param int $daysInMonth
     * 
     * @return array
     */
    private function  columnsMissingFromPreviousMonth(
        int $daysInMonth,
        int $daysInPrevMonth
    ) : array
    {   
        $cols = [];

        if($daysInMonth - $daysInPrevMonth == 1) $cols = ['31'];
        if($daysInMonth - $daysInPrevMonth == 2) $cols = ['30', '31'];
        if($daysInMonth - $daysInPrevMonth == 3) $cols = ['29', '30', '31'];

        return $cols;
    }

    /**
     * Create stats
     * 
     * @param int $group_id
     * @param String $prevDate
     * @param String $newDate
     * @param array $newRows
     * @param array $newCols
     * @return void
     */
    private function createStats(
        int $group_id,
        String $prevDate,
        String $newDate,
        array $newRows,
        array $newCols,
    ) : void
    {

        $colsWithValue = $this->getColsWithValue($newDate, $group_id);

        $stats = AnalyticStat::where('date', $prevDate)
            ->where('group_id', $group_id)
            ->get();

        $lastColumnId = 0;

        foreach ($stats as $stat) {

            $existsRowAndCol = array_key_exists($stat->row_id, $newRows)
                            && array_key_exists($stat->column_id, $newCols);
            
            if(!$existsRowAndCol) continue;

            $value      = $this->getValue($stat, $newRows, $newCols, $colsWithValue);
            $show_value = $this->getShowValue($stat, $newRows, $newCols, $colsWithValue);
            $lastColumnId = $newCols[$stat->column_id];

            AnalyticStat::create([
                'group_id'    => $stat->group_id,
                'date'        => $newDate,
                'row_id'      => $newRows[$stat->row_id],
                'column_id'   => $newCols[$stat->column_id],
                'value'       => $value,
                'show_value'  => $show_value,
                'activity_id' => $stat->activity_id,
                'editable'    => $stat->editable, 
                'class'       => $stat->class,
                'type'        => $stat->type,
                'comment'     => $stat->comment,
                'decimals'    => $stat->decimals,
            ]);
            
        }

        $lastColumnStats = AnalyticStat::query()
            ->where('column_id', $lastColumnId)
            ->where('group_id', $group_id)
            ->where('date', $newDate)
            ->get();

        /**
         * Дни в этом и в прошлом месяце.
         */
        $daysInMonth     = Carbon::parse($newDate)->daysInMonth;
        $daysInPrevMonth = Carbon::parse($prevDate)->daysInMonth;

        /**
         * Скрипт запускается если дни текущего месяца больше чем прошлый.
         */
        $analyticStats = [];

        if ($daysInMonth > $daysInPrevMonth)
        {
            $diffDays = array_diff($this->getCurrentMonthDayToArray($daysInMonth), $this->getPrevMonthDayToArray($daysInPrevMonth));
            foreach ($diffDays as $diffDay)
            {
                foreach ($lastColumnStats as $key => $columnStat)
                {
                    $analyticStats[] = [
                        'group_id'    => $columnStat->group_id,
                        'date'        => $newDate,
                        'row_id'      => $columnStat->row_id,
                        'column_id'   => ++$columnStat->column_id,
                        'value'       => '',
                        'show_value'  => $key == 0 ? $diffDay : '',
                        'activity_id' => null,
                        'editable'    => $columnStat->editable,
                        'class'       => $columnStat->class,
                        'type'        => $columnStat->type,
                        'comment'     => $columnStat->comment,
                        'decimals'    => $columnStat->decimals
                    ];
                }
            }

            DB::table('analytic_stats')->insert($analyticStats);
        }
    }

    /**
     * get cols that we will not nullify
     */
    private function getColsWithValue($date, $group_id) {
        return AnalyticColumn::where('date', $date)
            ->where('group_id', $group_id)
            ->whereIn('name', [
                'name',
                'plan',
                'sum',
                'avg',
            ])
            ->get('id')
            ->pluck('id')
            ->toArray();
    }

    /**
     * get value from AnalyticStat
     * 
     * @param AnalyticStat $stat
     * @param array $newCols
     * @param array $newRows
     * @param array $colsWithValue
     * 
     * @return string|int|null
     */
    private function getValue(
        AnalyticStat $stat,
        array $newRows,
        array $newCols,
        array $colsWithValue
    ) : string|int|null
    {
        $value = $stat->value;

        if($stat->type == 'remote' || $stat->type == 'inhouse') {
            $value = '';
        }

        if($stat->type == 'initial' || in_array($newCols[$stat->column_id], $colsWithValue)) {
            $value = '';
        }
        
        if($stat->type == 'formula') {
            $value = AnalyticStat::convert_formula_to_new_month($stat->value, $newRows, $newCols);
        }

        if($stat->row_id == array_values($newRows)[0]) {
            $value = $stat->value;
        }

        return $value;
    }

    /**
     * get show value from AnalyticStat
     * 
     * @param AnalyticStat $stat
     * @param array $newCols
     * @param array $newRows
     * @param array $colsWithValue
     * 
     * @return string|int|null
     */
    private function getShowValue(
        AnalyticStat $stat,
        array $newRows,
        array $newCols,
        array $colsWithValue
    ) : string|int|null
    {
        $value = '';
        $row_id = $newRows[$stat->row_id];
        $col_id = $newCols[$stat->column_id];
        
        if($row_id == array_values($newRows)[0]
           || $stat->type == 'formula') {
            $value = $stat->show_value;
        }

        if(in_array($newCols[$stat->column_id], $colsWithValue)) {
            $value = $stat->show_value;
        }

        if(in_array($stat->type, ['formula', 'avg', 'sum', 'salary'])) {
            $value = '';
        }

        return $value;
    }

}
