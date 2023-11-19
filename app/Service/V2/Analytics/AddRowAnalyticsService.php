<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\AddRowAnalyticsDto;
use App\DTO\Analytics\V2\CreateAnalyticDto;
use App\Helpers\DateHelper;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticStat;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddRowAnalyticsService
{
    /**
     * @param AddRowAnalyticsDto $dto
     * @return void
     * @throws Throwable
     */
    public function handle(AddRowAnalyticsDto $dto): void
    {
        try {
            DB::beginTransaction();
            $date = DateHelper::firstOfMonth($dto->year, $dto->month);
            $columns = AnalyticColumn::query()->where('group_id', $dto->groupId)
                ->where('date', $date)
                ->get();
            $ordersIndex = count($dto->rows);

            foreach ($dto->rows as $row)
            {
                $row = AnalyticRow::query()->create([
                    'group_id' => $dto->groupId,
                    'name' => $row['name'],
                    'date' => $date,
                    'order' => $ordersIndex--,
                ]);

                AnalyticStat::query()->create([
                    'type'      => $dto->rows['type'],
                    'date'      => $date,
                    'row_id'    => $row->id,
                    'column_id' => $columns->where('name', $row['type'])->first()->id,
                    'editable'  => 1,
                    'class'     => $row['index'] == 3 ? 'text-center font-bold bg-yellow' : 'text-center font-bold',
                    'value'     => 0,
                    'show_value' => 0
                ]);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }
}