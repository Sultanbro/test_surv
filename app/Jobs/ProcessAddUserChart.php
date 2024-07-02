<?php

namespace App\Jobs;

use App\DTO\WorkChart\User\AddUserChartDTO;
use App\Service\WorkChart\Users\AddUserChartService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAddUserChart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected AddUserChartDTO $dto;

    public function __construct(AddUserChartDTO $dto)
    {
        $this->dto = $dto;
    }

    /**
     * Execute the job.
     *
     * @param AddUserChartService $service
     * @return void
     * @throws Exception
     */
    public function handle(AddUserChartService $service): void
    {
        $service->handle($this->dto);
    }
}
