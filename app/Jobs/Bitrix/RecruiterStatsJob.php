<?php

namespace App\Jobs\Bitrix;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class RecruiterStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?string $count_last_hour;
    protected ?string $hour;
    protected ?string $date;
    protected ?string $user;
    protected string $command = 'tenants:run recruiter:stats --tenants=bp';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($count_last_hour = null, $hour = null, $date = null, $user = null)
    {
        $this->count_last_hour = $count_last_hour;
        $this->hour = $hour;
        $this->date = $date;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->command = $this->count_last_hour
            ? $this->command . ' --argument="count_last_hour=' . $this->count_last_hour . '"'
            : $this->command;
        $this->command = $this->hour
            ? $this->command . ' --argument="count_last_hour=' . $this->hour . '"'
            : $this->command;
        $this->command = $this->date
            ? $this->command . ' --argument="count_last_hour=' . $this->date . '"'
            : $this->command;
        $this->command = $this->user
            ? $this->command . ' --argument="count_last_hour=' . $this->user . '"'
            : $this->command;
        Artisan::call($this->command);
    }
}
