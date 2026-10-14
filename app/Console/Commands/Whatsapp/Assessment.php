<?php

namespace App\Console\Commands\Whatsapp;

use Illuminate\Console\Command;
use App\DayType;
use App\User;
use Carbon\Carbon;
use App\Models\Bitrix\Lead;
use App\Classes\Whatsapp;
use App\Classes\Helpers\Phone;

class Assessment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:assessment {quantity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправлять ссылку на оценку руководителя стажерам в первый и четвертые дни по 3 * 2  сообщений в минуту';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $day_1;
    public $day_4;

    /**
     * Sent messages counter
     */
    public $sent_1 = 0;
    public $sent_4 = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getDates();
        
        $day_1_users = $this->getUsers(1);
        $day_4_users = $this->getUsers(4);
        
      
        $this->line($day_4_users);

        foreach ($day_1_users as $user_id) {
            if($this->sent_1 <= (int)$this->argument('quantity')) {
                if($this->sendMsg($user_id)) {
                    $this->sent_1++;
                    $lead = Lead::where('user_id', $user_id)->first();
                    if($lead) {
                        $lead->received_assessment = 1;
                        $lead->save();
                    }
                }
            } else {
                break;
            }
        }

        $this->line('End of 1-day users');

        foreach ($day_4_users as $user_id) {
            if($this->sent_4 <= (int)$this->argument('quantity')) {
                if($this->sendMsg($user_id)) {
                    $this->sent_4++;
                    $lead = Lead::where('user_id', $user_id)->first();
                    if($lead) {
                        $lead->received_assessment = 2;
                        $lead->save();
                    }
                }
            } else {
                break;
            }
        }

        $this->line('End of 4-day users');
    }

    public function getDates() {
        $this->day_1 = date('Y-m-d');

        $day_4 = Carbon::now()->subDays(3);
        if($day_4->dayOfWeek == '6' || $day_4->dayOfWeek == '0') {
            $this->day_4 = $day_4->subDays(2)->format('Y-m-d');
        }
    }

    public function getUsers($day) {

        $date = $day == 1 ? $this->day_1 : $this->day_4;

        $assessment_statuses = $day == 1 ? [0] : [0,1];

        $leads = Lead::whereDate('invite_at', $date)
            ->whereIn('received_assessment', $assessment_statuses)
            ->where('user_id', '!=', 0)
            ->select('user_id')
            ->get()
            ->pluck('user_id')
            ->toArray();
       
        if($day == 1) {
            $users = [];
            foreach ($leads as $user_id){
                if(!$this->isAbsent($user_id)) {
                    array_push($users, $user_id);
                };
            }
        } else {
            $users = $leads;
        }

        return $users;
    }

    public function isAbsent($user_id) {
        $daytype = DayType::where('date', $this->day_1)->where('user_id', $user_id)->where('type', 2)->first();
        return $daytype ? true : false;
    }

    public function sendMsg($user_id) {
        $user = User::find($user_id);
        
        if($user) {
            $phone = Phone::normalize($user->phone);
            if($phone) {
                Whatsapp::send($phone, $this->msg($phone));
                $this->line('Sent: ' . $phone);
                usleep(4000000); // 4 sec
                return true;
            }
        }
        return false; 
    }

    public function msg($phone) {
        $line_break = '%0a';
        $link = 'https://'.tenant('id').'.jobtron.org/estimate_trainer?phone=' . $phone;

        $msg = 'Добрый день!' . $line_break; 
        $msg .= 'Оцените по 10 бальной шкале, насколько вы считаете, что тренер классно обучает:' . $line_break;
        $msg .= $link;

        return $msg;
    }
}
