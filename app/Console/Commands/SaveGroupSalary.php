<?php

namespace App\Console\Commands;

use App\Salary;
use App\GroupSalary;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SaveGroupSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:group {date?}';

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
     * @return mixed
     */
    public function handle()
    {

        if($this->argument('date')) {
            $dates = [$this->argument('date')];
        } else {
            $dates = [
                date('Y-m-d'),
                Carbon::now()->subMonth()->format('Y-m-d')
            ];
        }

        foreach ($dates as $key => $date) {
            $this->count($date);
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
            $this->comment('----------------------');
        }
    }

    public function count($date) {
        $groups = ProfileGroup::where('active', 1)->get();

        $workingGroups = Salary::getAllTotals($date, $groups, 1);
        $firedGroups = Salary::getAllTotals($date, $groups, 2);

        $date = Carbon::parse($date)->day(1)->format('Y-m-d');

        foreach ($groups as $key => $group) {
            $this->line($group->name);
            $this->line('Р:' . $workingGroups[$group->id]);
            $this->line('У:'. $firedGroups[$group->id]);
            $this->line('============');

            // save working
            $gs = GroupSalary::where('group_id', $group->id)->where('date', $date)->where('type', 1)->first();
            if($gs) {
                $gs->total = $workingGroups[$group->id];
                $gs->save();
            } else {
                GroupSalary::create([
                    'group_id' => $group->id,
                    'total' => $workingGroups[$group->id],
                    'type' => 1,
                    'date' => $date
                ]);
            }

            // save fired total
            $gs = GroupSalary::where('group_id', $group->id)->where('date', $date)->where('type', 2)->first();
            if($gs) {
                $gs->total = $firedGroups[$group->id];
                $gs->save();
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
