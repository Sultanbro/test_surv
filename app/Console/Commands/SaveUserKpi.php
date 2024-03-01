<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\Models\Kpi\Kpi;
use App\SavedKpi;
use App\Service\CalculateKpiService2;
use App\Service\KpiStatisticService;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SaveUserKpi extends Command
{
    public KpiStatisticService $repo;
    public CalculateKpiService2 $calculator;

    protected $signature = 'user:save_kpi {date?} {user_id?}';

    protected $description = 'Сохранить kpi';
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
        $query = Kpi::withTrashed()
            ->when($this->argument('user_id'), function (Builder $query) use ($date) {
                $query->where(function (Builder $query) use ($date) {
                    $query->where('targetable_id', $this->argument('user_id'));
                    $query->where('targetable_type', User::class);
                    $query->orWhereHas('users', fn($q) => $q
                        ->where('id', $this->argument('user_id'))
                        ->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $date->endOfMonth()));
                });
            })
            ->where(fn($query) => $query
                ->whereNull('kpis.deleted_at')
                ->orWhere('kpis.deleted_at', '>', $date->format('Y-m-d')));

        $filter['data_from']['year'] = $date->year;
        $filter['data_from']['month'] = $date->month;
        $filter['query_builder'] = $query;

        $kpis = $this->statisticService->fetchKpiGroupsAndUsers($filter);
        dd($kpis);
        $this->truncate($date, $this->argument('user_id'));
        $this->calc($kpis, $date, $this->argument('user_id'));
    }

    private function truncate(Carbon $date, $userId = null): void
    {
        DB::table('saved_kpi')
            ->when($userId, fn($query) => $query->where('user_id', $userId))
            ->where('date', '>=', $date->startOfMonth()->format("Y-m-d"))
            ->where('date', '<=', $date->endOfMonth()->format("Y-m-d"))
            ->delete();
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
                if ($userId) {
                    $users = Arr::where($users, fn($item) => $item['id'] == $userId);
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
}