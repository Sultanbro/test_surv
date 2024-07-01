<?php

namespace App\Console\Commands;

use App\DayType;
use App\Salary;
use App\Zarplata;
use Illuminate\Console\Command;

class SalaryTrainees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timetracking:salary_trainees {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Расчет оплаты за стажировки';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {

        $date = $this->argument('date') ?? date('Y-m-d');
        $default_zarplata = 70000;

        $daytypes = DayType::query()->where('type', 5)
            ->whereDate('date', $date)
            ->get()
            ->pluck('user_id')
            ->toArray();

        $daytypes = array_unique($daytypes);

        $salaries = Salary::query()
            ->whereDate('date', $date)
            ->whereIn('user_id', $daytypes)
            ->get();

        $zarplatas = Zarplata::query()
            ->whereIn('user_id', $daytypes)->get();


        $sum = 0;
        foreach ($daytypes as $user_id) {
            $salary = $salaries->where('user_id', $user_id)->first();

            $zarplata = $zarplatas->where('user_id', $user_id)->first();

            if (is_null($zarplata)) {

                Zarplata::query()->create([
                    'zarplata' => $default_zarplata,
                    'user_id' => $user_id,
                ]);
                $daily_salary = round($default_zarplata);
            } else {
                $oklad = $zarplata->zarplata == 0 ? $default_zarplata : $zarplata->zarplata;
                $daily_salary = round($oklad);
            }
            $this->line('daily_salary ' . $user_id . '  ' . $daily_salary);

            //$daily_salary = 0;
            if ($salary) {
                $salary->amount = $daily_salary;
                $salary->save();
            } else {
                Salary::query()->create([
                    'user_id' => $user_id,
                    'amount' => $daily_salary,
                    'date' => $date,
                ]);
            }

            $sum += $daily_salary;

        }
        $this->line('ИТОГО за ' . $date . '    ' . $sum . '  тг');
    }
}
