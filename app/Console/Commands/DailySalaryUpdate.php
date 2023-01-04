<?php

namespace App\Console\Commands;

use App\Salary;
use App\Zarplata;
use Illuminate\Console\Command;

class DailySalaryUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:update {date?}';  //php artisan salary:update  2022-04-12 
                                                       

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count daily salary amount'; // Считает сколько была сумма зарплаты на день. К примеру 70000, после сдачи экзамена стало 80000

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

        $argDate = $this->argument('date') ?? date('Y-m-d');
        
        $users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->select(['users.id'])
            
            ->where('ud.is_trainee', 0)
            ->get()
            ->pluck('id') 
            ->toArray(); 
        
        $users2 = \DB::table('users')
            ->whereNotNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->select(['users.id'])
            ->where('ud.is_trainee', 0)
            ->whereDate('deleted_at', '>=', $argDate)
            ->get()
            ->pluck('id') 
            ->toArray();

        $users = array_unique(array_merge($users, $users2));
        
        $salaries = Salary::where('date', $argDate)
            ->whereIn('user_id', $users)
            ->get();

        foreach($users as $key => $user_id) {
            $salary = $salaries->where('user_id', $user_id)->first();
            $zarplata = Zarplata::where('user_id', $user_id)->first();

            $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

            if($salary) {
                $this->line($key . '+ Начисление не изменено');
                continue;
            } 

            $this->line($key . '- Начисление обновлено');

            Salary::create([
                'user_id' => $user_id,
                'date' => $argDate, 
                'note' => '',
                'paid' => 0,
                'bonus' => 0,
                'comment_paid' => '',
                'comment_bonus' => '',
                'comment_award' => '',
                'amount' => $salary_amount,
            ]);
        }



    }

}
