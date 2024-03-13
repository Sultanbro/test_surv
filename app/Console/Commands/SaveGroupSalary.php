<?php

namespace App\Console\Commands;

use App\GroupSalary;
use App\ProfileGroup;
use App\Salary;
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
    protected $signature = 'salary:group {date?} {group_id?}';

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
     * @throws Exception
     */
    public function handle(): void
    {
        $from = Carbon::parse($this->argument('date') ?? now())->subMonth();
        $to = Carbon::parse($this->argument('date') ?? now());
        $this->count($from);
        $this->count($to);
    }

    /**
     * @throws Exception
     */
    public function count(Carbon $date): void
    {
        $dateToString = $date->format("Y-m-d");
        $groups = ProfileGroup::query()
            ->when($this->argument('group_id'), fn($query) => $query->where('id', $this->argument('group_id')))
            ->get();

        $workingGroups = Salary::getAllTotals($dateToString, $groups, Salary::WORKING_USERS);
        $firedGroups = Salary::getAllTotals($dateToString, $groups, Salary::FIRED_USERS);

        foreach ($groups as $group) {
            $this->line($group->name);
            $this->line('Р:' . $workingGroups[$group->id]);
            $this->line('У:' . $firedGroups[$group->id]);
            $this->line('дата:' . $dateToString);
            $this->line('============');

            // save working
            $workingGroupSalary = GroupSalary::query()
                ->where('group_id', $group->id)
                ->where('date', $date)
                ->where('type', 1)
                ->first();

            if ($workingGroupSalary) {
                $workingGroupSalary->total = $workingGroups[$group->id];
                $workingGroupSalary->save();
            } else {
                GroupSalary::query()
                    ->create([
                        'group_id' => $group->id,
                        'total' => $workingGroups[$group->id],
                        'type' => 1,
                        'date' => $date
                    ]);
            }

            // save fired total
            $firedGroupSalary = GroupSalary::query()
                ->where('group_id', $group->id)
                ->where('date', $dateToString)
                ->where('type', 2)
                ->first();
            if ($firedGroupSalary) {
                $firedGroupSalary->total = $firedGroups[$group->id];
                $firedGroupSalary->save();
            } else {
                GroupSalary::query()
                    ->create([
                        'group_id' => $group->id,
                        'total' => $firedGroups[$group->id],
                        'type' => 2,
                        'date' => $dateToString
                    ]);
            }
        }
    }
}
