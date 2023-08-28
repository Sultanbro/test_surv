<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Trainee;
use App\ProfileGroup;
use App\User;
use App\DayType;
use App\Salary;
use App\Zarplata;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $date = $this->argument('date') ?? date('Y-m-d');
        $default_zarplata = 70000;

        $groups = ProfileGroup::where('active', 1)->get();

		// $users = [];
		// foreach($groups as $group) {
  //           if($group->paid_internship == 0) continue;
		// 	$users  = array_merge($users, json_decode($group->users));
		// 	$users = array_unique($users);
		// }

		// $workers = User::whereIn('id', $users)->get()->pluck('id')->toArray();
        
        // dump(count($workers));
		// $trainees = Trainee::whereNull('applied')->whereIn('user_id', $workers)->get()->toArray();
        // dump(count($trainees));

        $daytypes = DayType::whereIn('type', [5,7])
            ->whereDate('date', $date)
            ->get()
            ->pluck('user_id')
            ->toArray();

        $daytypes = array_unique($daytypes);

        $salaries = Salary::whereDate('date', $date)->whereIn('user_id', $daytypes)->get();

        $zarplatas = Zarplata::whereIn('user_id', $daytypes)->get();

        
        $sum = 0;
        foreach ($daytypes as $user_id) {
            $salary = $salaries->where('user_id', $user_id)->first();

            $zarplata = $zarplatas->where('user_id', $user_id)->first();

            if(is_null($zarplata)) {

                Zarplata::create([
                    'zarplata' => $default_zarplata,
                    'user_id' => $user_id,
                ]);
                $daily_salary = round($default_zarplata); 
                $this->line('daily_salary ' . $user_id . '  ' . $daily_salary);
            } else {
                $oklad = $zarplata->zarplata == 0 ? $default_zarplata : $zarplata->zarplata;
                $daily_salary = round($oklad); 
                $this->line('daily_salary ' . $user_id . '  ' . $daily_salary);
            }
             
            //$daily_salary = 0;
            if($salary) {
                $salary->amount = $daily_salary;
                $salary->save();
            } else {
                Salary::create([
                    'user_id' => $user_id,
                    'amount' => $daily_salary,
                    'date' => $date,
                ]);
            }

            $sum+= $daily_salary;
            
        }

        $this->line('ИТОГО за ' . $date . '    ' . $sum . '  тг');
        
    }
}
