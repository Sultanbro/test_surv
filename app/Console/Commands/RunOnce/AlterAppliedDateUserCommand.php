<?php

namespace App\Console\Commands\RunOnce;

use App\Timetracking;
use App\User;
use App\UserDescription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AlterAppliedDateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'applied:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $chunked = $this->userDescriptions()->chunk(50);
        foreach ($chunked as $descriptions) {
            foreach ($descriptions as $description) {
                $time = Timetracking::query()
                    ->where('user_id', $description->user_id)
                    ->where('status', 0)
                    ->orderByDesc('enter')
                    ->first()?->toArray();
                if ($time) {
                    $description->update([
                        'applied' => $time['enter']
                    ]);
                }
                else {
                    $this->line("Not found timtracking $description->user_id");
                }
            }
            sleep(1);
        }
    }

    private function userDescriptions()
    {
        return UserDescription::query()
            ->whereNull('applied')
            ->where(DB::raw('YEAR(created_at)'), '=', '2023')
            ->get();
    }
}
