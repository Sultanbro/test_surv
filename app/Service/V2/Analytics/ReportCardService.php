<?php
declare(strict_types=1);

namespace App\Service\V2\Analytics;

use App\DTO\Analytics\V2\ReportCardDto;
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\ReportCard;
use App\Timetracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Класс для работы с Service.
 */
class ReportCardService
{
    /**
     * @param ReportCardDto $dto
     * @return bool
     * @throws Throwable
     */
    public function handle(ReportCardDto $dto): bool
    {
        $firstOfMonth = Carbon::createFromDate($dto->year, $dto->month)->firstOfMonth()->format('Y-m-d');
        $columns = AnalyticColumn::query()
            ->where('date', $firstOfMonth)
            ->where('group_id', $dto->groupId)
            ->whereNotIn('name', ['name', 'sum', 'avg', 'plan'])
            ->get();

        try {
            DB::beginTransaction();
            $this->saveReportCards($dto, $firstOfMonth);
            foreach ($columns as $column) {
                $date = Carbon::createFromDate($dto->year, $dto->month, $column->name)->format('Y-m-d');
                $stat = AnalyticStat::query()
                    ->where('date', $firstOfMonth)
                    ->where('row_id', $dto->rowId)
                    ->where('column_id', $column->id)
                    ->first();
                dd($stat);
                if ($stat) {
                    $dayTotal = Timetracking::totalHours($date, $dto->groupId, $dto->positions);
                    $dayTotal = floor($dayTotal / 9 * 10 / $dto->divide);
                    $stat->update([
                        'day_total' => $dayTotal,
                        'show_value' => $dayTotal,
                        'type' => AnalyticStat::TIME,
                    ]);
                }
            }
            DB::commit();
            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param ReportCardDto $dto
     * @param string $date
     * @return void
     */
    private
    function saveReportCards(ReportCardDto $dto, string $date): void
    {
        $reportCards = [];

        foreach ($dto->positions as $position) {
            $reportCards[] = [
                'position_id' => $position,
                'group_id' => $dto->groupId,
                'date' => $date,
                'divide_to' => $dto->divide
            ];
        }
        ReportCard::query()->where('group_id', $dto->groupId)->delete();

        ReportCard::query()->where('group_id', $dto->groupId)->insert($reportCards);

    }
}