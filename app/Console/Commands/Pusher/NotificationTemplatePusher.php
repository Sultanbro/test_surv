<?php

namespace App\Console\Commands\Pusher;

use App\Models\Mailing\MailingNotification;
use Illuminate\Console\Command;

class NotificationTemplatePusher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:pusher:template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить шаблонное уведомление.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifications = MailingNotification::query()->where('is_template', 1)->get();
        
    }
}
