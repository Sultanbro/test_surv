<?php

namespace App\Console\Commands\Analytics;

use App\Models\Analytics\DecompositionValue;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

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

    public function handle(): void
    {
        $this->cleanDuplicates();
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
            ->groupBy('group_id', 'date', 'name')
            ->get();

        foreach ($tops as $top) {
            TopValue::query()
                ->updateOrCreate([
                    'name' => $top->name,
                    'group_id' => $top->group_id,
                    'date' => $currentDate,
                    'activity_id' => $top->activity_id,
                    'unit' => $top->unit,
                    'options' => $top->options],
                    [
                        'value' => 0,
                        'min_value' => $top->min_value,
                        'max_value' => $top->max_value,
                        'cell' => $top->cell,
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
                ->updateOrCreate([
                    'date' => $currentDate,
                    'group_id' => $dec->group_id,
                    'name' => $dec->name,
                ], [
                    'values' => []
                ]);
        }
    }

    /**
     * @throws Throwable
     */
    private function cleanDuplicates(): void
    {
        DB::transaction(function () {
            // Select the minimum id for each group
            $minIds = DB::table('decomposition_values')
                ->selectRaw('MIN(id) as id')
                ->groupBy('name', 'group_id', 'date')
                ->pluck('id');

            // Delete records that are not in the set of minimum ids
            DB::table('decomposition_values')
                ->whereNotIn('id', $minIds)
                ->delete();
        });
        DB::transaction(function () {

            DB::table('top_values')
                ->whereIn('id', function ($query) {
                    $query->select('id')
                        ->from(function ($subQuery) {
                            $subQuery->selectRaw('id, ROW_NUMBER() OVER (PARTITION BY name, group_id, date ORDER BY id) as rn')
                                ->from('top_values')
                                ->whereDate('date', now())
                                ->groupBy('id', 'name', 'group_id', 'date');
                        }, 'RankedValues')
                        ->where('rn', '>', 1);
                })
                ->delete();
        });
    }
}
