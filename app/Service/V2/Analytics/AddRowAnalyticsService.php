<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\AddRowAnalyticsDto;
use App\Models\Analytics\AnalyticColumn as Column;
use App\Models\Analytics\AnalyticRow;
use App\Models\Analytics\AnalyticRow as Row;
use App\Models\Analytics\AnalyticStat;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
* Класс для работы с Service.
*/
class AddRowAnalyticsService
{
    /**
     * @param AddRowAnalyticsDto $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(AddRowAnalyticsDto $dto): bool
    {
        DB::beginTransaction();

        $afterRow = AnalyticRow::query()->findOrFail($dto->afterRowId);

        $newRow = AnalyticRow::query()->create([
            'group_id'  => $dto->groupId,
            'date'      => $dto->date,
            'name'      => 'name',
            'order'     => ++$afterRow->order,
        ]);

        $this->rowOrderManage($dto->groupId, $dto->date, $afterRow->order);

        $this->createStat($dto, $newRow);

        DB::commit();

        return true;
    }

    /**
     * @param AddRowAnalyticsDto $dto
     * @param Row $row
     * @return void
     */
    private function createStat(AddRowAnalyticsDto $dto, AnalyticRow $row): void
    {
        Column::query()->where('group_id', $dto->groupId)
            ->where('date', $dto->date)
            ->get()->map(function ($column) use ($dto, $row) {
                $type = ($column->name == AnalyticStat::SUM) ? AnalyticStat::SUM : (($column->name == AnalyticStat::AVG) ? AnalyticStat::AVG : AnalyticStat::INITIAL);
                $class = $column->name == 'name' ? 'text-left' : 'text-center';

                AnalyticStat::query()->create([
                    'group_id'  => $dto->groupId,
                    'date'      => $dto->date,
                    'row_id'    => $row->id,
                    'column_id' => $column->id,
                    'value'     => '',
                    'show_value' => '',
                    'editable'  => 1,
                    'type'      => $type,
                    'class'     => $class
                ]);
            });
    }

    /**
     * @param int $groupId
     * @param string $date
     * @param int $order
     * @return void
     */
    private function rowOrderManage(
        int $groupId,
        string $date,
        int $order
    ): void
    {
        Row::query()->where('group_id', $groupId)
            ->where('date', $date)
            ->where('order', '>=', $order)
            ->orderBy('order', 'asc')
            ->get()->map(function ($row) use ($order) {
                $row->order = $order++;
            });
    }
}