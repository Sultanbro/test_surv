<?php

namespace App\Console\Commands\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\ProfileGroup;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

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
        
        if(Carbon::now()->day != 1) return false;

        $newDate  = date('Y-m-d');
        $prevDate = Carbon::now()->subMonth()->format('Y-m-d');
        
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
        $newCols = [];

        $cols = AnalyticColumn::where('date', $prevDate)
                    ->where('group_id', $group_id)
                    ->orderBy('order','asc')
                    ->get();

        foreach($cols as $col) {
            $newCol = AnalyticColumn::create([
                'group_id' => $col->group_id,
                'name'     => $col->name,
                'date'     => $newDate,
                'order'    => $col->order,
            ]);

            $newCols[$col->id] = $newCol->id;
        }

        return $newCols;
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

        $stats = AnalyticStat::query()
            ->where('date', $prevDate)
            ->where('group_id', $group_id)
            ->get();

        foreach ($stats as $stat) {

            $existsRowAndCol = array_key_exists($stat->row_id, $newRows)
                            && array_key_exists($stat->column_id, $newCols);
            
            if($existsRowAndCol) {
                AnalyticStat::create([
                    'group_id'    => $stat->group_id,
                    'date'        => $newDate,
                    'row_id'      => $newRows[$stat->row_id],
                    'column_id'   => $newCols[$stat->column_id],
                    'value'       => $stat->value,
                    'show_value'  => in_array($newCols[$stat->column_id], $colsWithValue) ? $stat->show_value : '',
                    'activity_id' => $stat->activity_id,
                    'editable'    => $stat->editable, 
                    'class'       => $stat->class,
                    'type'        => $stat->type,
                    'comment'     => $stat->comment,
                    'decimals'    => $stat->decimals,
                ]);
            }
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

}
