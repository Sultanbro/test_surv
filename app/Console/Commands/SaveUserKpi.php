<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\SavedKpi;
use App\Service\CalculateKpiService2;
use App\Service\KpiStatisticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SaveUserKpi extends Command
{

    protected $signature = 'user:save_kpi {date?} {user_id?}';

    protected $description = 'Сохранить kpi';

    public KpiStatisticService $repo;

    public CalculateKpiService2 $calculator;

    private KpiStatisticService $statisticService;

    public function __construct(
        KpiStatisticService  $repo,
        CalculateKpiService2 $calculator,
        KpiStatisticService  $statisticService,
    )
    {
        parent::__construct();
        $this->repo = $repo;
        $this->calculator = $calculator;
        $this->statisticService = $statisticService;
    }

    public function handle(): void
    {
        $date = Carbon::parse($this->argument('date') ?? now())
            ->day(1);

        // get kpis
        $kpis = $this->statisticService->kpis($date)->get();
        $this->calc($kpis, $date);
    }

    private function calc($kpis, Carbon $date): void
    {
        foreach ($kpis as $kpi) {
            $kpi->kpi_items = [];
            if ($kpi->histories_latest) {
                $payload = json_decode($kpi->histories_latest->payload, true);

                if (isset($payload['children'])) {
                    $kpi->items = $kpi->items->whereIn('id', $payload['children']);
                }
            }
            try {

                $users = $this->statisticService->getUsersForKpi($kpi, $date);
                foreach ($users as $user) {
                    $total = 0;
                    foreach ($user['items'] as $item) {
                        $total += $this->calculator->calcSum($item, $kpi->toArray());
                    }
                    if ($kpi->id === 94 && $user['id'] == 6401) {
                        dd($total);
                    }
                    $this->updateSavedKpi([
                        'total' => $total,
                        'user_id' => $user['id'],
                        'date' => $date->format("Y-m-d")
                    ]);
                }
            } catch (RuntimeException $e) {
                Log::error($e);
                continue;
            }
        }
    }

    private function updateSavedKpi(array $data): void
    {
        /** @var SavedKpi $sk */
        $sk = SavedKpi::query()->where('user_id', $data['user_id'])
            ->where('date', $data['date'])
            ->first();

        if ($sk) {
            $sk->total = $data['total'];
            $sk->save();
            $date = $sk->date;
        } else {
            SavedKpi::query()->create($data);
            $date = $data['date'];
        }

        if ($date) {
            $date = Carbon::createFromFormat('Y-m-d', $data['date']);
            event(new KpiChangedEvent($date));
        }
    }
}
