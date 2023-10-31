<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Exception;
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

    public function handle(): void
    {
        $endDate = Carbon::parse($this->argument('date')) ?? now();
        $startDate = $endDate->subDays(10);

        // Get all users within the date range using whereBetween
        $users = User::query()
            ->withWhereHas('user_description', fn($query) => $query->where('is_trainee', false))
            ->with(['salaries' => fn($query) => $query->whereBetween('date', [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")])])
            ->with('zarplata')
            ->where(fn($query) => $query
                ->whereNull('deleted_at')
                ->orWhere(fn($query) => $query->whereBetween('deleted_at', [$startDate->format("Y-m-d"), $endDate->format("Y-m-d")]))
            )
            ->get();

        while ($startDate <= $endDate) {
            $date = $startDate->format("Y-m-d");

            foreach ($users as $key => $user) {
                // Find the salary for the user
                $salary = $user->salaries->first();

                // Find the zarplata for the user
                $zarplata = $user->zarplata;

                $salary_amount = $zarplata ? $zarplata->zarplata : 70000;

                if ($salary) {
                    $this->line($key . '+ Начисление не изменено');
                    continue;
                }

                $this->line($key . '- Начисление обновлено');

                // Error handling for salary creation
                try {
                    $user->salaries()->create([
                        'date' => $date,
                        'note' => '',
                        'paid' => 0,
                        'bonus' => 0,
                        'comment_paid' => '',
                        'comment_bonus' => '',
                        'comment_award' => '',
                        'amount' => $salary_amount,
                    ]);
                } catch (Exception $e) {
                    // Handle the error (log it, report it, or take appropriate action)
                    $this->error('Error creating salary for user: ' . $e->getMessage());
                }
            }

            $startDate->addDay();
        }
    }
}
