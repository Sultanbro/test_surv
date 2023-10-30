<?php

namespace App\Console\Commands\Bitrix;

use App\Api\BitrixOld;
use App\Models\Bitrix\Lead;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class SyncBitrixSegment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:segments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle(BitrixOld $service): void
    {
        $chunked = $this->leads()->chunk(50);
        foreach ($chunked as $leads) {
            foreach ($leads as $lead) {
                $leadFromBitrix = $service->getLeads(lead_id: $lead->lead_id);
                if (array_key_exists('result', $leadFromBitrix) && array_key_exists('UF_CRM_1498210379', $leadFromBitrix['result'])) {
                    $segment = $leadFromBitrix['result']['UF_CRM_1498210379'];
                    // user ---> segment $leadFromBitrix->segment
                    // lead ---> segment $leadFromBitrix->segment
                }
            }
            sleep(2);
        }
    }

    private function leads(): Collection
    {
        return Lead::query()
            ->where('segment', 99)
            ->get();
    }
}
