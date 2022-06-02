<?php

namespace App\Console\Commands;

use App\Components\TelegramBot;
use App\Salary;
use App\User;
use App\Exam;
use App\UserFine;
use App\Account;
use App\DayType;
use App\Zarplata;

use App\Setting;
use App\Timetracking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

const UCALLS = 1;

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
        
        $users = User::leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->select(['users.id'])
            
            ->where('ud.is_trainee', 0)
            ->get()
            ->pluck('id') 
            ->toArray(); 
        
        $users2 = User::onlyTrashed()->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
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

            $this->checkIfUserPassExam($user_id, $salary_amount);

            if($salary) {
                dump($key . '+');
                $salary->amount = $salary_amount;
                $salary->save();
            } else {
                dump($key . '-');
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

    public function checkIfUserPassExam($user_id, &$salary_amount)
    {   
        $date = $this->argument('date') ?? date('Y-m-d');
        $date = Carbon::parse($date);

        $bonusFromExam = 0; // бонус от экзамена
        $exam = Exam::where('user_id', $user_id) // Проверка сдавал ли сотрудник книгу в этом месяце
            ->where('month', $date->month)
            ->where('year', $date->year)
            ->first();
    
        if(!is_null($exam) && $exam->success == 1) {
            $bonusFromExam = 10000;
            $salary_amount += $bonusFromExam;
        }    
    }

}
