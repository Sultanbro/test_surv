<?php

namespace App\Console\Commands\Cache;

use App\DTO\Analytics\V2\GetRentabilityDto;
use App\Service\V2\Analytics\GetRentabilityService;
use Cache;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CacheTopRentabilityPerDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:rentability {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The command to cache the section of top rentability';

    public function handle(GetRentabilityService $rentabilityService): void
    {
        $date = Carbon::parse($this->argument('date') ?? now());
        $cacheKey = $date->isCurrentMonth() ? now()->format("Y-m-d") : $date->format('Y-m');
        $cacheKey .= 'getRentability';
        $dto = GetRentabilityDto::fromArray([
            'year' => $date->year,
            'month' => $date->month,
        ]);
        Cache::driver('central')
            ->rememberForever($cacheKey, fn() => $rentabilityService->handle($dto));
    }
}
