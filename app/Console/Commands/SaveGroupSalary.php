<?php

namespace App\Console\Commands;

use App\GroupSalary;
use App\Models\Analytics\TopValue;
use App\ProfileGroup;
use App\Salary;
use Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SaveGroupSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:group {date?} {group?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Сохранить заработанное группой без вычета шт и ав';

    /**
     * Variables that used
     *
     * @var mixed
     */
    public $date; // Дата пересчета

    public function handle(): void
    {
        $givenDate = Carbon::parse($this->argument('date') ?? now())->startOfMonth()->format('Y-m-d');
        $pervMonth = Carbon::parse($this->argument('date') ?? now())->subMonth()->startOfMonth()->format('Y-m-d');

        $dates = [
            $pervMonth,
            $givenDate
        ];

        foreach ($dates as $date) {
            $this->count($date);
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            Cache::driver('central')->forget(TopValue::rentabilityCacheKey(Carbon::parse($date)));
        }
    }

    /**
     * @throws Exception
     */
    public function count($date)
    {
        if ($this->argument('group')) {
            $groups = ProfileGroup::query()
                ->where('id', $this->argument('group'))
                ->where('active', 1)
                ->get();
        } else {
            $groups = ProfileGroup::query()->where('active', 1)->get();
        }


        $workingGroups = Salary::getAllTotals($date, $groups, Salary::WORKING_USERS);
        $firedGroups = Salary::getAllTotals($date, $groups, Salary::FIRED_USERS);

        $date = Carbon::parse($date)->firstOfMonth();

        foreach ($groups as $key => $group) {
            $this->line($group->name);
            $this->line('Р:' . $workingGroups[$group->id]);
            $this->line('У:' . $firedGroups[$group->id]);
            $this->line('============');

            // save working
            $workingGroupSalary = GroupSalary::where('group_id', $group->id)
                ->where('date', $date)
                ->where('type', 1)
                ->first();
            if ($workingGroupSalary) {
                $workingGroupSalary->total = $workingGroups[$group->id];
                $workingGroupSalary->save();
            } else {
                GroupSalary::create([
                    'group_id' => $group->id,
                    'total' => $workingGroups[$group->id],
                    'type' => 1,
                    'date' => $date
                ]);
            }

            // save fired total
            $firedGroupSalary = GroupSalary::where('group_id', $group->id)->where('date', $date)->where('type', 2)->first();
            if ($firedGroupSalary) {
                $firedGroupSalary->total = $firedGroups[$group->id];
                $firedGroupSalary->save();
            } else {
                GroupSalary::create([
                    'group_id' => $group->id,
                    'total' => $firedGroups[$group->id],
                    'type' => 2,
                    'date' => $date
                ]);
            }
        }

    }
}
