<?php

declare(strict_types=1);

namespace App\Jobs\Tenancy;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CreateS3Bucket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var TenantWithDatabase */
    protected $tenant;

    public function __construct(TenantWithDatabase $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ProcessFailedException
     */
    public function handle()
    {
        $process = new Process([
			'sudo',
			'/root/s3-curl/s3curl.pl',
			'--id=storage',
			'--createBucket',
			'--',
			config('filesystems.disks.s3.endpoint').'/tenant'.$this->tenant->id
		]);

		$process->mustRun();

		$code = $process->getExitCode();
		$output = $process->getOutput();
	
        if($output !== '') {
            throw new \Exception($output);
        } 
    }
}
