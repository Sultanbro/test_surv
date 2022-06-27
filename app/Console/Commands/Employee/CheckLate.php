<?php

namespace App\Console\Commands\Employee;

use App\User;
use App\UserFine;
use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:late {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Штрафы за опоздание';
    
    /**
     * 
     */
    protected $day;

    /**
     * 'Y-m-d'
     */
    protected $date;

    /**
     * 
     */
    protected $user;

    /**
     * 'Y-m-d'
     */
    protected $startOfMonth;

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

        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();
        $this->date = $this->argument('date') ? $this->argument('date') : date('Y-m-d');
        $this->day = $date->day;
        $this->startOfMonth = $date->startOfMonth()->format('Y-m-d');

        $users = User::with('user_description')
            ->whereHas('user_description', function ($query) {
                $query->where('is_trainee', 0);
            })
            ->orderBy('last_name', 'asc')
            ->select(['users.id','users.last_name', 'users.name', 'users.working_time_id', 'users.work_start'])
            ->get();

     
        foreach($users as $user) {
            $this->user = $user;
            $this->checkLate();
        }
        
     
    }

    public function checkLate() {
        
        $workStart = $this->user->work_starts_at(); // Время начала смены для юзера
        
        dump($this->user->last_name . ' ' . $this->user->name . ' ' . $workStart);

        $dateTimeStart = Timetracking::where('user_id', $this->user->id) // Время начала работы, первый enter
            ->whereDate('enter', $this->date)
            ->min('enter');

        if (!is_null($dateTimeStart)) {
            $userFineModel = new UserFine();
            $day = date("Y-m-d", strtotime($dateTimeStart));
            $shiftStartTimeInSeconds = strtotime($day.' '.$workStart); 
            $workStartTimeInSeconds = strtotime($dateTimeStart);
            
            $customShiftStartTimeInSeconds = $shiftStartTimeInSeconds - 600; // Время начала смены, нужно до 10 минут приходить на работу 
            $diffInSeconds = ($workStartTimeInSeconds - $customShiftStartTimeInSeconds); // Разница в секундах

            $this->coming_earlier = 0;
            if($workStartTimeInSeconds - $shiftStartTimeInSeconds > 0) {
                $this->coming_earlier = round(($workStartTimeInSeconds - $shiftStartTimeInSeconds)/60);
            }

            if ($diffInSeconds <= 0) {
                //  пришел вовремя");
            } else if ($diffInSeconds > 0) { // Опоздал на $diffInMinutes минут
                $diffInMinutes = round($diffInSeconds/60); 
                
                if ($diffInMinutes <= 5) { // Штраф до 5 минут

                    $activeFineLessFiveMinutes = $userFineModel->where([ // сначала ищем активные штрафы
                        'user_id' => (int)$this->user->id,
                        'fine_id' => 2,
                        'day' => $this->date,
                        'status' => UserFine::STATUS_ACTIVE,
                    ])->first();

                    if (is_null($activeFineLessFiveMinutes)) { // если их нет, тогда ищем не активные штрафы
                        
                        $inactiveFineLessFiveMinutes = $userFineModel->where([ 
                            'user_id' => (int)$this->user->id,
                            'fine_id' => 2,
                            'day' => $this->date,
                            'status' => UserFine::STATUS_INACTIVE,
                        ])->first();
                        
                        if (is_null($inactiveFineLessFiveMinutes)) { // если и их нет, тогда уже добавляем штраф
                            $userFineModel->addUserFine([
                                'user_id' => (int)$this->user->id,
                                'fine_id' => 2,
                                'day' => $this->date,
                                'status' => UserFine::STATUS_ACTIVE,
                                'note' => Null
                            ]);

                            $this->history('За приход на работу с опозданием до 5 минут');
                        }
                    }
                   
                } else if ($diffInMinutes > 5) { // если он не успел прийти на 5 минут раньше штраф 1000

                    $fineMoreFiveMinutes = $userFineModel->where([
                        'user_id' => (int)$this->user->id,
                        'fine_id' => 1,
                        'day' => $this->date,
                        'status' => UserFine::STATUS_ACTIVE,
                    ])->first();

                    if (is_null($fineMoreFiveMinutes)) { // если их нет, тогда ищем не активные штрафы

                        $inactiveFineMoreFiveMinutes = $userFineModel->where([
                            'user_id' => (int)$this->user->id,
                            'fine_id' => 1,
                            'day' => $this->date,
                            'status' => UserFine::STATUS_INACTIVE,
                        ])->first();
                        
                        if (is_null($inactiveFineMoreFiveMinutes)) { // если и их нет, тогда уже добавляем штраф
                            $userFineModel->addUserFine([
                                'user_id' => (int)$this->user->id,
                                'fine_id' => 1,
                                'day' => $this->date,
                                'status' => UserFine::STATUS_ACTIVE,
                                'note' => Null
                            ]);

                            $this->history('За приход на работу с опозданием от 5 минут и более');
                        }
                    }
                }
            }
        } else {} // Сотрудник $this->user->id не вышел 
            
    }

    public function history($message){
        $th = TimetrackingHistory::whereDate('date', $this->date)
            ->where('user_id',  $this->user->id)
            ->where('description', 'like', $message)
            ->first();

        if(!$th) {
            TimetrackingHistory::create([
                'user_id' => $this->user->id,
                'author_id' => 5,
                'author' => 'Система',
                'date' => $this->date,
                'description' => $message,
            ]);
        }
    }
}
