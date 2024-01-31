<?php

namespace App\Jobs\Salary;

use App\Service\Salary\UpdateSalaryInterface;
use Carbon\Carbon;
use Couchbase\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessUpdateSalary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $groupId;
    private string $date;

    public function __construct(string $date, int $groupId)
    {
        $this->groupId = $groupId;
        $this->date =  $date;
    }

    /**
     * Execute the job.
     *
     * @param UpdateSalaryInterface $service
     * @return void
     */
    public function handle(UpdateSalaryInterface $service): void
    {
        $service->touch($this->date, $this->groupId);
    }
}
