<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\Kpi;
use App\SavedKpi;
use App\Service\CalculateKpiService2;
use App\Service\KpiStatisticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        $kpis = $this->statisticService->kpis(
            $date,
            [
                'only_active' => false
            ],
            Kpi::withTrashed()->where(fn($query) => $query
                ->whereNull('kpis.deleted_at')
                ->orWhere(fn($query) => $query->whereDate('kpis.deleted_at', '>', $date->format('Y-m-d'))))
        )
            ->get();

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

                foreach ($kpi->items as $item) {
                    $history = $item->histories->whereBetween('created_at', [$date->startOfMonth(), $date->endOfMonth()])->first();
                    $has_edited_plan = $history ? json_decode($history->payload, true) : false;
                    $item['daily_plan'] = (float)$item->plan;
                    if ($has_edited_plan) {
                        if (array_key_exists('plan', $has_edited_plan)) $item['daily_plan'] = $has_edited_plan['plan'];
                        if (array_key_exists('name', $has_edited_plan)) $item['name'] = $has_edited_plan['name'];
                        if (array_key_exists('share', $has_edited_plan)) $item['share'] = $has_edited_plan['share'];
                        if (array_key_exists('method', $has_edited_plan)) $item['method'] = $has_edited_plan['method'];
                        if (array_key_exists('unit', $has_edited_plan)) $item['unit'] = $has_edited_plan['unit'];
                        if (array_key_exists('cell', $has_edited_plan)) $item['cell'] = $has_edited_plan['cell'];
                        if (array_key_exists('common', $has_edited_plan)) $item['common'] = $has_edited_plan['common'];
                        if (array_key_exists('percent', $has_edited_plan)) $item['percent'] = $has_edited_plan['percent'];
                        if (array_key_exists('sum', $has_edited_plan)) $item['sum'] = $has_edited_plan['sum'];
                        if (array_key_exists('group_id', $has_edited_plan)) $item['group_id'] = $has_edited_plan['group_id'];
                        if (array_key_exists('activity_id', $has_edited_plan)) $item['activity_id'] = $has_edited_plan['activity_id'];
                    }
                    $item['plan'] = $item['daily_plan'];
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
