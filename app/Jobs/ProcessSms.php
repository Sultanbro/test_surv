<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contact;
use App\Group;
use App\Expense;
use App\Message;
use App\Robot;
use App\Sms\Smpp\SMSCounter;
use App\Sms\SmsSender;
use App\Statistic;
use App\User;
use App\Autocall;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message, $type, $statistic, $sms_message, $phone, $lines, $desc, $default_status;
    protected $user_id, $id_belong, $id_contact, $robot_type;

    /**
     * Create a new job instance.
     * $message => Message | Autocall
     * $type => Message::SMS_TYPE | Autocall::$SMS_TYPE | Message::INTEGRATION_TYPE
     * $statistic => Contact | Statistic
     *
     * @return void
     */
    public function __construct($message, $type, $statistic, $sms_message, $phone, $lines, $desc, $default_status)
    {
        $this->message = $message;
        $this->statistic = $statistic;
        $this->type = $type;
        $this->sms_message = $sms_message;
        $this->phone = $phone;
        $this->lines = $lines;
        $this->desc = $desc;
        $this->default_status = $default_status;

        $this->user_id = $message->user_id;
        

        if($type == Message::SMS_TYPE || $type == Autocall::$SMS_TYPE) {
            $this->id_belong = $message->id;
            $this->robot_type = Robot::TYPE_SONIC_SMS_FORWARD;
        } else {
            $this->id_belong = $message->id_belong;
            $this->robot_type = Robot::TYPE_API_MESSAGE_FORWARD;
        }

        if($type == Message::SMS_TYPE) {
            $this->id_contact = $statistic->id;
        } else if($type == Autocall::$SMS_TYPE){
            $this->id_contact = $statistic->id_contact;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $smsCounter = new SMSCounter();
        $countObj = $smsCounter->count($this->sms_message);

        $count_message = $countObj->messages;

        // $line = SmsSender::send($this->phone, $this->sms_message, $this->lines);
        Log::info('Phone: '.$this->phone.', Message: '.$this->sms_message);
        $line = 'kcell';

        $status = ($line > -1 || $line == 'kcell'|| $line == 'beeline') ? 1 : $this->default_status;

        Log::info('send sms '.$this->phone.' line '.$line);

        $sms_cost = 0;
        $data = [];

        if($status == 1) {
            if($this->type == Message::SMS_TYPE) {
                $sms_cost = Message::MESSAGE_COST;
            } else if($this->type == Autocall::$SMS_TYPE) {
                $sms_cost = Autocall::$SMS_COST;
            } else {
                $sms_cost = Message::INTEGRATION_COST;
            }

            if($this->type == Message::SMS_TYPE) {
                if(in_array($this->user_id,[624])) {
                    $sms_cost = 6.5;
                }
            }

            if($this->type == Message::INTEGRATION_TYPE) {
                if(in_array($this->user_id,[624])) {
                    $sms_cost = 6.5;
                }
            }

            $sms_cost = $sms_cost * $count_message;

            $expense_id = Expense::add($sms_cost, $this->user_id, $this->type, $this->desc);

            Log::info('expence '.$expense_id.' cost '.$sms_cost);

            $data = [
                'msg_count' => $count_message,
                'id_expense' => $expense_id,
                'time' => time()
            ];

            if($this->type == Autocall::$SMS_TYPE) {
                $this->statistic->has_sms = 1;
                $this->statistic->save();
            }
        }

        if($this->type == Message::SMS_TYPE || $this->type == Autocall::$SMS_TYPE) {
            $sms_statistic = Statistic::where('id_user', $this->user_id)
                ->where('id_belong', $this->id_belong)
                ->where('type', $this->type)
                ->where('id_contact', $this->id_contact)->first();
    
            if ($sms_statistic) {
                $stat_id = $sms_statistic->id;
                $sms_statistic->status = $status;
                if($this->type == Message::SMS_TYPE) {
                    $sms_statistic->number = $this->phone;
                }
                $sms_statistic->cost = $sms_cost;
                $sms_statistic->date = DB::raw('NOW()');
                $sms_statistic->line = $line;
                $sms_statistic->data = json_encode($data);
                $sms_statistic->save();
            } else {
                $stat_id = Statistic::create([
                    'id_user' => $this->user_id,
                    'id_belong' => $this->id_belong,
                    'id_contact' => $this->id_contact,
                    'status' => $status,
                    'number' => $this->phone,
                    'cost' => $sms_cost,
                    'type' => $this->type,
                    'date' => DB::raw('NOW()'),
                    'text' => $this->sms_message,
                    'line' => $line,
                    'data' => json_encode($data),
                ])->id;
                Log::info('create stat '.$stat_id.' cost '.$sms_cost);
            }
        } else if($this->type == Message::INTEGRATION_TYPE) {
            $this->statistic->status = $status;
            $this->statistic->cost = $sms_cost;
            // $this->statistic->date = DB::raw('NOW()');
            $this->statistic->line = $line;
            $this->statistic->data = json_encode($data);
            $this->statistic->save();

            $stat_id = $this->statistic->id;
        }

        // if($this->type == Message::SMS_TYPE || $this->type == Message::INTEGRATION_TYPE) {
        //     if($status == 1) {
        //         $robots = Robot::where('user_id', $this->user_id)->where('entity_id', $this->id_belong)->where('type', Robot::TYPE_SONIC_SMS_FORWARD)->where('status', 1)->get();
    
        //         /**
        //          * @var $robot  Robot
        //          */
        //         foreach ($robots as $robot) {
        //             $robot_stat_id = $robot->play($this->phone, date("Y-m-d H:i:s"), $stat_id);
        //         }
        //     }
        // }

        sleep(5);
        Log::info('End sms forward');
    }
}
