<?php

namespace App\Console\Commands;

use App\ProfileGroup;
use App\User;
use App\UserDescription;
use App\Models\Bitrix\Lead;
use Illuminate\Console\Command;
use App\Http\Controllers\IntellectController as IC;
use App\DayType;
use App\Classes\Helpers\Phone;
use Carbon\Carbon;


class InviteToBitrixAndAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'intellect:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправить сообщение со ссылкой по ватсапу на учет времени и битрикс, приглашенным стажерам на 4ый день стажировки';

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
        $from = Carbon::now()->subDays(14)->format('Y-m-d'); 
		$to = Carbon::now()->subDays(1)->format('Y-m-d'); 

        $trainees_ids = UserDescription::whereNull('applied')->where('is_trainee', 1)->where('bitrix', 0)->get()->pluck('user_id')->toArray(); 

        $daytypes = DayType::whereBetween('date', [$from, $to])->whereIn('type', [5,7])->whereIn('user_id' , $trainees_ids)->get(); // 5 Стажировка, 7 вернулся на стажировку 
        $daytypes = $daytypes->filter(function ($item) {
            return $item->date->isWeekday();
        })->groupBy('user_id');

        
        $user_ids = [];
        foreach($daytypes as $key => $dt) {
            if($dt->count() == 4) {
                array_push($user_ids, $key);
            }
        }

        
        $users = User::whereIn('id', $user_ids)->get();

        $x = 0; // просто посчитать 

        $whatsapp = new IC();

        //$trainees = Trainee::whereNull('applied')->where('bitrix', 0)->get(); 

        foreach($users as $user) {
           
       
            $x++;
            $wphone = Phone::normalize($user->phone);
            //dump($user->id . '   == ' . $wphone);

            
            if(is_null($wphone)) {
                //dump($user->id);
                continue; 
            }
            $invite_link = 'https://infinitys.bitrix24.kz/?secret=bbqdx89w';
            $whatsapp->send_msg($wphone, 'Ваша ссылка для регистрации в портале Битрикс24: %0a'. $invite_link . '.  %0a%0aВойти в учет времени: https://'.tenant('id').'.jobtron.org/login. %0aЛогин: ' . $user->email . ' %0aПароль: 12345.');
            usleep(10000000); // 10 sec
            
            // $trainee = $trainees->where('user_id', $user->id)->first();
            // if($trainee) {
            //     $trainee->bitrix = 1;
            //     $trainee->save();
            // }

            UserDescription::make([
                'user_id' => $user->id,
                'bitrix' => 1,
            ]);
            
        }

        ///dump($x . ' END OF SENDING');

        
    }
}