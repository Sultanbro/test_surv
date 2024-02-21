<?php

namespace App\Console\Commands;

use App\Service\Referral\ForReferrerDaily;
use App\Timetracking;
use App\User;
use Illuminate\Console\Command;

class CheckTimetrackers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:timetrackers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда при запуске завершает день для всех пользователей которые не нажали на ЗАВЕРШИТЬ ДЕНЬ';

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
     * @param ForReferrerDaily $service
     * @return void
     */
    public function handle(ForReferrerDaily $service): void
    {
        $this->line('Checking for the end of the day for users');

        $recordsFromTimeTrack = Timetracking::query()
            ->whereDate('enter', date('Y-m-d'))
            ->whereNull('exit')
            ->get();

        foreach ($recordsFromTimeTrack as $recordFromTimeTrack) {
            /**@var User $user */
            $user = $recordFromTimeTrack->user;

            if ($user) {
                $userSchedule = $user->schedule();
                $recordFromTimeTrack->update([
                    'exit' => $userSchedule['end']->subHours($user->timezone)
                        ->format('Y-m-d H:i:s')
                ]);
            }
        }
        // update referral daily salaries
        $service->handle();
    }
}
