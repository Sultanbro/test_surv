<?php

namespace App\Console\Commands\Employee;

use Illuminate\Console\Command;
use App\User;
use App\Zarplata;
use Carbon\Carbon;
use App\Position;
use App\UserNotification;
use App\SalaryIndexation as IndexationHistory;

class SalaryIndexation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary:indexation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Индексация зарплаты каждые 3 месяца стажа';

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
        // Приняты в BP
        $users = \DB::table('users')
            ->whereNull('deleted_at')
            ->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
            ->where('is_trainee', 0)
            //->where('users.id', 5) // При запуске на прод убрать
            ->select('users.id', 'ud.applied', 'ud.is_trainee','users.created_at', 'users.position_id')
            ->get();

        foreach($users as $user) {
            $days_from_applied = Carbon::parse($user->applied)->diff(now())->days;
            $ih = IndexationHistory::where('user_id', $user->id)->first(); 

            if(!$ih) {
                IndexationHistory::create([
                    'user_id' => $user->id,
                    'step' => 0,
                ]);  
            } 

            $step = $ih ? $ih->step : 0; // На каком шаге индексации сейчас

            if($days_from_applied == 0 && $user->is_trainee == 0) { // Если это не стажер и нет даты принятия (Старые пользователи)
                $days_from_applied = Carbon::parse($user->created_at)->diff(now())->days;
            }
            
            $index_value = $this->getIndexValue($user->position_id);

            if($days_from_applied >= 90 && $days_from_applied < 180 && $step == 0) { // первая индексация
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0) {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 180 && $days_from_applied < 270 && $step == 1) { // вторая индексация
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 270 && $days_from_applied < 360  && $step == 2) { // 3
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 360 && $days_from_applied < 450  && $step == 3) { // 4
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 450 && $days_from_applied < 540  && $step == 4) { // 5
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 540 && $days_from_applied < 630  && $step == 5) { // 6
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 630 && $days_from_applied < 720  && $step == 6) { // 7
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }

            if($days_from_applied >= 720 && $days_from_applied < 810  && $step == 7) { // 8
                $oklad = $this->updateZarplata($user->id, $index_value);
                if($oklad > 0)  {
                    $this->notifyUser($user->id, $oklad, $index_value);
                }
            }


        }
    }

     /**
     * Увеличить оклад
     */
    protected function updateZarplata($user_id, $value) {
        
        $zarplata = Zarplata::where('user_id', $user_id)->first();

        $oklad = 0;
       
        if($zarplata && $value > 0) { 
            $this->line('ID '. $user_id . ' повышение оклада на ' . $value);

            $oklad = (int)$zarplata->zarplata + (int)$value;
            $zarplata->zarplata = $oklad;
            $zarplata->save();


            $ih = IndexationHistory::where('user_id', $user_id)->first();
            $ih->step = $ih->step + 1;
            $ih->save();
        }

        return $oklad;
    }

    /**
     * Сумма индексации
     */
    protected function getIndexValue($position_id) {
        $pos = Position::find($position_id);
        return $pos && $pos->indexation == 1 ? $pos->sum : 0;
    }

    /**
     * Сумма индексации
     */
    protected function notifyUser($user_id, $oklad, $index_value) {
        
        if($index_value > 0) {
            UserNotification::create([
                'user_id' => $user_id,
                'about_id' => $user_id,
                'title' => 'Индексация зарплаты',
                'group' => now(),
                'message' => 'Поздравляем! <br>Ваша зарплата проиндексирована на сумму ' . $index_value . ' тенге (KZT).<br>Ваша зарплата составляет ' . $oklad . 'тенге (KZT)'
            ]);
        }
        
    }

    
}