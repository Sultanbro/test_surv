<?php

namespace App\Console\Commands\Bitrix;

use Illuminate\Console\Command;
use App\Service\Integrations\BitrixIntegrationService;
use App\Api\BitrixOld as Bitrix;
use Illuminate\Http\Client\HttpClientException;

class MoveTraineesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:trainees:move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private BitrixIntegrationService $service;
    private Bitrix $bitrix;

    /**
     * Execute the console command.
     *
     * @return int
     * @throws HttpClientException
     */
    public function handle(): int
    {
        $this->service = new BitrixIntegrationService;
        $this->bitrix = new Bitrix;

        $deals = $this->service->getDeals([
            'FILTER' => [
                'STAGE_ID' => 'C4:21'
            ],
        ]);

        $next = $deals['next'] ?? null;

        $trainees = $deals['result'];

        while ($next !== null) {
            usleep(2000000); // 2 sec
            $new_deals = $this->service->getDeals([
                'start' => $next,
                'FILTER' => [
                    'STAGE_ID' => 'C4:21',
                ],
            ]);
            $next = $new_deals['next'];
            $trainees = array_merge($trainees, $new_deals['result']); 
        }

        foreach ($trainees as $trainee) {
            $this->moveTrainee($trainee['ID']);
        }

        return Command::SUCCESS;
    }

    private function moveTrainee($deal_id)
    {
        usleep(2000000); // 2 sec
        $this->bitrix->changeDeal($deal_id, [
            'STAGE_ID' => 'C4:18'
        ]);
    }
}
