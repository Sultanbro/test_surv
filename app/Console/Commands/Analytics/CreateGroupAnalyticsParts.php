<?php

namespace App\Console\Commands\Analytics;

use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateGroupAnalyticsParts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:parts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create TopValues and Decomposition';

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
            
            $this->createTopValues($group->id, $prevDate, $newDate);
            $this->createDecomposition($group->id, $prevDate, $newDate);
            
        }

        $this->line('end');
    }

    /**
     * Create createTopValues
     * 
     * @param int $group_id
     * @param String $prevDate
     * @param String $newDate
     * @return void
     */
    private function createTopValues(int $group_id, String $prevDate, String $newDate) : void
    {
        $tops = TopValue::where([
            'group_id' => $group_id,
            'date'     => $prevDate,
        ])->get();

        foreach($tops as $top) {
            TopValue::create([
                'name'        => $top->name,
                'group_id'    => $top->group_id,
                'date'        => $newDate,
                'value'       => 0,
                'unit'        => $top->unit,
                'options'     => $top->options,
                'min_value'   => $top->min_value,
                'max_value'   => $top->max_value,
                'cell'        => $top->cell,
                'activity_id' => $top->activity_id,
                'round'       => $top->round,
                'is_main'     => $top->is_main,
                'fixed'       => $top->fixed,
                'value_type'  => $top->value_type,
                'reversed'    => $top->reversed,
            ]);
        }  
    }

    /**
     * Create createDecomposition
     * 
     * @param int $group_id
     * @param String $prevDate
     * @param String $newDate
     * @return void
     */
    private function createDecomposition(int $group_id, String $prevDate, String $newDate) : void
    {
        $decs = DecompositionValue::where([
            'group_id' => $group_id,
            'date'     => $prevDate,
        ])->get();

        foreach($decs as $dec) {
            DecompositionValue::create([
                'date'     => $newDate,
                'group_id' => $dec->group_id,
                'name'     => $dec->name,
                'values'   => []
            ]);
        }
    }

}
