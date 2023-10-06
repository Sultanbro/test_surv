<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
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
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->repository = new UserRepository();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        $date = $this->argument('date') ?? date('Y-m-d');
        $users = $this->repository
            ->getUsersWithDescription($date)
            ->with(['salaries' => fn($query) => $query->where('date', $date)])
            ->get();
        $userIds = $users->pluck('id')->toArray();
        $salaries = Salary::where('date', $date)
            ->whereIn('user_id', $userIds)
            ->get();

        foreach ($userIds as $key => $user_id) {
            $salary = $salaries->where('user_id', $user_id)->first();
            $zarplata = Zarplata::where('user_id', $user_id)->first();

            $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

            if ($salary) {
                $this->line($key . '+ Начисление не изменено');
                continue;
            }

            $this->line($key . '- Начисление обновлено');

            Salary::create([
                'user_id' => $user_id,
                'date' => $date,
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
