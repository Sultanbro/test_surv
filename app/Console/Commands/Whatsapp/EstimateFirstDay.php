<?php

namespace App\Console\Commands\Whatsapp;

use Illuminate\Console\Command;
use App\DayType;
use App\User;
use Carbon\Carbon;
use App\Models\Bitrix\Lead;
use App\Classes\Whatsapp;
use App\Classes\Helpers\Phone;
use DB;

class EstimateFirstDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:estimate_first_day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправлять ссылку на оценку по первому дню обучения';
    
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
       
        $users = $this->getUsers();
        $this->comment(count($users));
        $this->comment(json_encode($users));

        foreach ($users as $user_id) {
            if($this->sendMsg($user_id)) {
                $lead = Lead::where('user_id', $user_id)->first();
                if($lead) {
                    $lead->received_fd = 1;
                    $lead->save();
                }
            } else {
                $this->line($user_id . ' not send link');
            }
        }  

        $this->comment('End of users');

      
    }

    public function getUsers() {

        $minutes = 360 - 150; // тут есть косяк с временем, в bitrix_leads записали 9-30 (utc+6) стало 15-30 
        $leads = Lead::where('invite_at', '<', DB::raw("CURRENT_TIMESTAMP() + INTERVAL " . $minutes . " MINUTE"))
            ->whereDate('invite_at', '>', Carbon::now()->subDays(7)->format('Y-m-d')) // 150 min
            ->whereNotNull('invite_at')
            ->where('received_fd', 0) 
            ->where('user_id', '!=', 0)
            ->select('user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();
       
        $users = [];
        foreach ($leads as $user_id){ 
            if(!$this->isAbsent($user_id)) {
                array_push($users, $user_id);
            };
        }

        return $users;
    }

    public function isAbsent($user_id) {
        $daytype = DayType::where('date', date('Y-m-d'))->where('user_id', $user_id)->where('type', 2)->first();
        return $daytype ? true : false;
    }

    public function sendMsg($user_id) {
        $user = User::find($user_id);
        
        if($user) {
            $phone = Phone::normalize($user->phone);
            if($phone) {
                Whatsapp::send($phone, $this->msg($user->id));
                $this->line('Sent: ' . $phone);
                usleep(6000000); // 5 sec
                return true;
            }
        }
        return false; 
    }

    public function msg($id) {
        $line_break = '%0a';
        $link = 'https://'.tenant('id').'.jobtron.org/efd?phone=' . $id;

        $msg = 'Добрый день!' . $line_break; 
        $msg .= 'Пройдите небольшой анонимный опрос, чтобы оценить качество обучения вашим тренером\руководителем:' . $line_break;
        $msg .= $link;

        return $msg;
    }
}
