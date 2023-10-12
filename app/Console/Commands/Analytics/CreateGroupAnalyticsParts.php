<?php

namespace App\Console\Commands\Analytics;

use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

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
    public function handle(): void
    {
        if (Carbon::now()->day !== 1) {
            $this->warn("Cant create pivot tables because today isn't beginning of month");
            return;
        }

        $this->line('start creating pivot tables:');

        $currentDate = Carbon::now()
            ->format('Y-m-d');
        $previousDate = Carbon::now()
            ->subMonth()
            ->format('Y-m-d');

        $groups = ProfileGroup::query()
            ->where('active', 1)
            ->where('has_analytics', 1)
            ->get();

        foreach ($groups as $group) {

            $this->line('group ' . $group->id . ' ' . $group->name);

            $this->createTopValues($group->id, $previousDate, $currentDate);
            $this->createDecomposition($group->id, $previousDate, $currentDate);

        }

        $this->line('end');
    }

    /**
     * Create createTopValues
     *
     * @param int $group_id
     * @param String $previousDate
     * @param String $currentDate
     * @return void
     */
    private function createTopValues(int $group_id, string $previousDate, string $currentDate): void
    {
        /** @var Collection<TopValue> $tops */
        $tops = TopValue::query()
            ->where([
                'group_id' => $group_id,
                'date' => $previousDate,
            ])
            ->get();

        foreach ($tops as $top) {
//            $cloned = $top->replicate(); why dont use this
//            $cloned->date = $currentDate;
//            $cloned->value = 0;
//            $cloned->save();

            TopValue::query()
                ->create([
                    'name' => $top->name,
                    'group_id' => $top->group_id,
                    'date' => $currentDate,
                    'value' => 0,
                    'unit' => $top->unit,
                    'options' => $top->options,
                    'min_value' => $top->min_value,
                    'max_value' => $top->max_value,
                    'cell' => $top->cell,
                    'activity_id' => $top->activity_id,
                    'round' => $top->round,
                    'is_main' => $top->is_main,
                    'fixed' => $top->fixed,
                    'value_type' => $top->value_type,
                    'reversed' => $top->reversed,
                ]);
        }
    }

    /**
     * Create createDecomposition
     *
     * @param int $group_id
     * @param String $previousDate
     * @param String $currentDate
     * @return void
     */
    private function createDecomposition(int $group_id, string $previousDate, string $currentDate): void
    {
        $decompositions = DecompositionValue::query()
            ->where([
                'group_id' => $group_id,
                'date' => $previousDate,
            ])
            ->get();

        foreach ($decompositions as $dec) {
            DecompositionValue::query()
                ->create([
                    'date' => $currentDate,
                    'group_id' => $dec->group_id,
                    'name' => $dec->name,
                    'values' => []
                ]);
        }
    }
}
