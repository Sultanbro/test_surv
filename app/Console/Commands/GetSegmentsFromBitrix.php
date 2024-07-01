<?php

namespace App\Console\Commands;

use App\Api\BitrixOld as Bitrix;
use App\Models\Bitrix\Lead;
use Illuminate\Console\Command;

class GetSegmentsFromBitrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:segments';


    public function handle()
    {
        // 4147
        $leads = Lead::query()
            ->whereMonth('created_at', '9')
            ->whereYear('created_at', '2022')
            ->where('segment', 99)
            ->get();

        $count = $leads->count();

        foreach ($leads as $key => $lead) {
            // TODO: check in prod
            $lead->segment = $this->getSegmentAndSaveForLead($lead->lead_id);
            $lead->save();

            $this->line(($key + 1) . ' from ' . $count);
            usleep(1000000);
        }


    }

    private function getSegmentAndSaveForLead($id)
    {

        $res = (new Bitrix)->getLeads(0, '', 'ALL', 'ASC', '2010-01-01', '2050-01-01', "DATE_CREATE", $id, 'title');

        $segment = 999;

        if (array_key_exists('result', $res) && array_key_exists('UF_CRM_1498210379', $res['result'])) {
            $segment = $res['result']['UF_CRM_1498210379'];

            $segment = Lead::getSegmentAlt($segment);
        }

        return $segment;

    }
}