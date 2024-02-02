<?php

namespace App\Console\Commands;

use App\Events\KpiChangedEvent;
use App\SavedKpi;
use App\Service\CalculateKpiService;
use App\Service\KpiStatisticService;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SaveUserKpi extends Command
{
    protected $signature = 'user:save_kpi {date?} {user_id?}';  //php artisan user:save_kpi 2022-08-01 // целый месяц , долго

    protected $description = 'Сохранить kpi';
    public KpiStatisticService $repo;

    public CalculateKpiService $calculator;

    public array $workdays;
    private KpiStatisticService $statisticService;

    public function __construct(
        KpiStatisticService $repo,
        CalculateKpiService $calculator,
        KpiStatisticService $statisticService,
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
        $userId = $this->argument('user_id');

        // count workdays in month
        $this->workdays = [];
        $this->workdays[5] = workdays(Carbon::parse($date)->year, Carbon::parse($date)->month);
        $this->workdays[6] = workdays(Carbon::parse($date)->year, Carbon::parse($date)->month, [0]);

        // get kpis
        $kpis = $this->statisticService->kpis($date)->get();
        $this->calc($kpis, $date);
    }

    private function calc($kpis, $date): void
    {
        foreach ($kpis as $kpi) {
            $users = $this->statisticService->getAverageKpiPercent($kpi, $date);
            dd($users);
        }
    }

    private function updateSavedKpi(array $data): void
    {
        // save
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
