<?php

namespace App\Console\Commands;

use App\Exam;
use App\User;
use App\Zarplata;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExamZarpMonthly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:zarp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zeroing bonuses received after passing the exam on books';

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
        $previousMonth = new Carbon('first day of last month');

        $users = User::Join('exams', function($q) use ($previousMonth)
            {
                $q->on('users.id', '=', 'exams.user_id')
                    ->where('exams.month', $previousMonth->format("m"))
                    ->where('exams.year', $previousMonth->format("Y"))
                    ->where('exams.bonus', 1)
                    ->where('exams.success', 1);
            })
            ->get(['users.id as id','exams.id as exam_id']);

        foreach ($users as $user) {

            $zarp = Zarplata::where('user_id', $user->id)->first();

            if($zarp){
                $zarp->zarplata = $zarp->zarplata-10000;
                $zarp->save();
                if($zarp){
                    $exam = Exam::find($user->exam_id);
                    $exam->description = $exam->description . '0' . ' - ' . now() . '; ';
                    $exam->bonus = 0;
                    $exam->save();
                }
            }

        }

    }
}