<?php

namespace App\Console\Commands\Notifications;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\UserNotification;
use App\Models\User\NotificationTemplate;

class FillReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usernotification:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Уведолмение о заполнении отчета за неделю';

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
        if(date('w') != '5') return ''; 

        $receivers = json_decode(NotificationTemplate::find(9)->ids); // Уведомление Заполните отчет

        $users = User::where('UF_ADMIN', 1)
            ->where('position_id', $receivers)
            ->get();
        
        $from = Carbon::now()->subDays(4)->format('d.m.Y');
        $to = Carbon::now()->format('d.m.Y');

        foreach($users as $user) {

                $msg_fragment = 'Заполните отчет о том, какую необычно полезную работу Вы сделали на этой неделе. <br>';
                $msg_fragment .= 'C ' . $from . ' по ' . $to . '<br>';
                $msg_fragment .= '<a class="btn btn-primary mt-2 rounded btn-sm report-modal">Заполнить отчет</a>';

                UserNotification::create([
                    'user_id' => $user->id,
                    'about_id' => 0,
                    'title' => 'Заполните отчет',
                    'group' => now(),
                    'message' => $msg_fragment
                ]);
        }


        
    }

   
}