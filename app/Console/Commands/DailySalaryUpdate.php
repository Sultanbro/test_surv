<?php

namespace App\Console\Commands;

use App\Service\Salary\UpdateSalaryInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailySalaryUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
        protected $signature = 'salary:update {date?} {group?} {month?}';  //php artisan salary:update  2022-04-12

    public function __construct(
        private readonly UpdateSalaryInterface $updateSalary
    )
    {
        parent::__construct();
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count daily salary amount'; // Считает сколько была сумма зарплаты на день. К примеру 70000, после сдачи экзамена стало 80000

    public function handle(): void
    {
        $date = $this->argument('date') ?? now()->format('Y-m-d');
        $groupId = $this->argument('group');
        if ($this->argument("month")) {
            $from = Carbon::parse($date)->startOfMonth();
            $to = Carbon::parse($date)->endOfMonth();
            while ($from <= $to) {
                $this->updateSalary->touch($from->format("Y-m-d"), $groupId);
                $from->addDay();
            }
            return;
        }
        $this->updateSalary->touch($date, $groupId);
    }
}
