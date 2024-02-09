<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\SavedKpi;
use App\Service\CalculateKpiService2;
use App\Service\KpiStatisticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use function Psy\debug;

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
            ->startOfMonth();
        // get kpis
        $kpis = $this->statisticService->kpis($date, [
            'only_active' => false
        ])->get();
        $this->truncate($date, $this->argument('user_id'));
        $this->calc($kpis, $date, $this->argument('user_id'));
    }

    private function calc($kpis, Carbon $date, $userId = null): void
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
                dd($users);
                if ($userId) {
                    $users = Arr::where($users, fn($item) => $users['id'] == $userId);
                }

                foreach ($users as $user) {
                    $total = 0;

                    foreach ($user['items'] as $item) {
                        $total += $this->calculator->calcSum($item, $kpi->toArray());
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

        $exists = SavedKpi::query()
            ->where([
                'date' => $data['date'],
                'user_id' => $data['user_id'],
            ])->first();

        if ($exists) {
            $exists->total += $data['total'];
            $exists->save();
        } else {
            SavedKpi::query()->create([
                'date' => $data['date'],
                'user_id' => $data['user_id'],
                'total' => $data['total']
            ]);
        }

        $date = Carbon::createFromFormat('Y-m-d', $data['date']);
        event(new KpiChangedEvent($date));
    }

    private function truncate(Carbon $date, $userId = null): void
    {
        DB::table('saved_kpi')
            ->when($userId, fn($query) => $query->where('user_id', $userId))
            ->where('date', '>=', $date->startOfMonth()->format("Y-m-d"))
            ->where('date', '<=', $date->endOfMonth()->format("Y-m-d"))
            ->delete();
    }
}
