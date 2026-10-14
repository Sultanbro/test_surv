<?php

namespace App\Console\Commands\RunOnce;

use App\Api\Bitrix\LeadApi;
use App\Models\Bitrix\Lead;
use App\Models\Bitrix\Segment;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'One time command';

    public function handle(LeadApi $service): void
    {
        $chunked = $this->leads()->chunk(3);
        foreach ($chunked as $leads) {
            foreach ($leads as $lead) {

                $leadFromBitrix = $service->get($lead->lead_id);
                if (array_key_exists('result', $leadFromBitrix) && array_key_exists('UF_CRM_1498210379', $leadFromBitrix['result'])) {
                    $segment = $leadFromBitrix['result']['UF_CRM_1498210379'];
                    if (array_key_exists($segment, Segment::BITRIX_SEGMENTS)) {
                        $name = Segment::BITRIX_SEGMENTS[$leadFromBitrix['result']['UF_CRM_1498210379']];
                        $local_segment = Segment::query()->where('name', $name)->first();
                        if ($local_segment) {
                            $lead->update([
                                'segment' => $local_segment->id
                            ]);
                        } else {
                            $this->info("Segment: $name ; net v nashey baze!");
                        }
                    } else {
                        $this->info("Segment id: $segment ; net v nashey spiske!");
                    }
                }
                sleep(1);
            }
        }
    }

    private function leads(): Collection
    {
        return Lead::query()
            ->whereIn('segment', [0, 99])
            ->where( DB::raw('YEAR(created_at)'), '=', '2023')
            ->get();
    }
}
