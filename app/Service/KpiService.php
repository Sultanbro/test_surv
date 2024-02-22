<?php

namespace App\Service;

use App\Events\KpiChangedEvent;
use App\Events\TrackKpiItemEvent;
use App\Events\TrackKpiUpdatesEvent;
use App\Exceptions\Kpi\TargetDuplicateException;
use App\Filters\Kpis\KpiFilter;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\Position;
use App\ProfileGroup;
use App\Traits\KpiHelperTrait;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class KpiService
{
    use KpiHelperTrait;

    public function fetch($filters): array
    {
        $searchWord = $filters['query'] ?? null;
        $dateToFilter = $filters['data_from'] ?? null;
        $date = [
            'year' => $dateToFilter['year'] ?? now()->year,
            'month' => $dateToFilter['month'] ?? now()->month,
        ];

        $date = Carbon::createFromDate($date['year'], $date['month']);
        $endOfDate = $date->endOfMonth()->format('Y-m-d');
        $startOfDate = $date->startOfMonth()->format('Y-m-d');
        $groupId = $filters['group_id'] ?? null;

        $kpis = Kpi::query()
            ->when($searchWord, fn() => (new KpiFilter)->globalSearch($searchWord))
            ->when($groupId, function (Builder $subQuery) use ($groupId) {
                $subQuery->where('targetable_id', $groupId);
                $subQuery->orWhereRelation(
                    relation: 'groups',
                    column: 'kpiable_id',
                    operator: '=',
                    value: $groupId
                );
            })
            ->with([
                'items' => function (HasMany $query) use ($endOfDate, $startOfDate) {
                    $query->with(['histories' => function (MorphMany $query) use ($endOfDate, $startOfDate) {
                        $query->whereBetween('created_at', [$startOfDate, $endOfDate]);
                    }]);
                    $query->where(function (Builder $query) use ($startOfDate, $endOfDate) {
                        $query->whereNull('deleted_at');
                        $query->orWhere('deleted_at', '>', $endOfDate);
                    });
                },
                'user' => fn(HasOne $query) => $query->select('id'),
                'user.groups' => fn(BelongsToMany $query) => $query->select('name')->where('status', 'active'),
                'creator',
                'updater',
                'histories' => function (morphMany $query) use ($startOfDate, $endOfDate) {
                    $query->whereBetween('created_at', [$startOfDate, $endOfDate]);
                },
                'histories_latest' => function ($query) use ($endOfDate) {
                    $query->whereDate('created_at', '<=', $endOfDate);
                }
            ])
            ->where(function ($query) use ($startOfDate) {
                $query->whereHas('targetable', function ($q) use ($startOfDate) {
                    if ($q->getModel() instanceof User) {
                        $q->whereNull('deleted_at')
                            ->orWhereDate('deleted_at', '>', $startOfDate);
                    } elseif ($q->getModel() instanceof Position) {
                        $q->whereNull('deleted_at')
                            ->orWhereDate('deleted_at', '>', $startOfDate);
                    } elseif ($q->getModel() instanceof ProfileGroup) {
                        $q->where('active', 1);
                    }
                })
                    ->orWhereHas('users', fn($q) => $q->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $startOfDate))
                    ->orWhereHas('positions', fn($q) => $q->whereNull('deleted_at')
                        ->orWhereDate('deleted_at', '>', $startOfDate))
                    ->orWhereHas('groups', fn($q) => $q->where('active', 1));
            })
            ->with([
                'users' => fn($q) => $q->whereNull('deleted_at')
                    ->orWhereDate('deleted_at', '>', $startOfDate),
                'positions' => fn($q) => $q->whereNull('deleted_at')
                    ->orWhereDate('deleted_at', '>', $startOfDate),
                'groups' => fn($q) => $q->where('active', 1),
            ])
            ->whereDate('kpis.created_at', '<=', $endOfDate)
            ->get();
        $kpis_final = [];

        foreach ($kpis as $kpi) {

            $item = $kpi->toArray();
            // remove items if it's not in history
            if ($kpi->histories->first()) {
                $payload = json_decode($kpi->histories->first()->payload, true);

                $items = $kpi->items;

                if (isset($payload['children'])) {
                    $items = $items->whereIn('id', $payload['children']);
                }

                foreach ($items as $_item) {

                    $history = $_item->histories->whereBetween('created_at', [$startOfDate, $endOfDate])->first();

                    $has_edited_plan = $history ? json_decode($history->payload, true) : false;

                    /**
                     * fields from history
                     */
                    $_item['daily_plan'] = (float)$_item->plan;

                    if ($has_edited_plan) {
                        if (array_key_exists('plan', $has_edited_plan)) $_item['daily_plan'] = $has_edited_plan['plan'];
                        if (array_key_exists('name', $has_edited_plan)) $_item['name'] = $has_edited_plan['name'];
                        if (array_key_exists('share', $has_edited_plan)) $_item['share'] = $has_edited_plan['share'];
                        if (array_key_exists('method', $has_edited_plan)) $_item['method'] = $has_edited_plan['method'];
                        if (array_key_exists('unit', $has_edited_plan)) $_item['unit'] = $has_edited_plan['unit'];
                        if (array_key_exists('cell', $has_edited_plan)) $_item['cell'] = $has_edited_plan['cell'];
                        if (array_key_exists('common', $has_edited_plan)) $_item['common'] = $has_edited_plan['common'];
                        if (array_key_exists('percent', $has_edited_plan)) $_item['percent'] = $has_edited_plan['percent'];
                        if (array_key_exists('sum', $has_edited_plan)) $_item['sum'] = $has_edited_plan['sum'];
                        if (array_key_exists('group_id', $has_edited_plan)) $_item['group_id'] = $has_edited_plan['group_id'];
                        if (array_key_exists('activity_id', $has_edited_plan)) $_item['activity_id'] = $has_edited_plan['activity_id'];
                    }

                    $_item['plan'] = $_item['daily_plan'];
                }
                $item['items'] = $items->values();
            }

            $kpis_final[] = $item;
        }

        return [
            'kpis' => $kpis_final,
            'activities' => Activity::query()
                ->get(),
            'groups' => ProfileGroup::query()
                ->where('active', 1)
                ->get()
                ->pluck('name', 'id')
                ->toArray(),
        ];
    }

    /**
     * Change kpi off_limit property, off_limit -> If employee do his activity bigger than 100%, then he will get more kpi_bonus
     * @param Request $request
     * @return true
     */
    public function setOffLimit(Request $request): bool
    {
        $kpi = Kpi::query()->findOrFail($request->get('id'));

        $kpi->update([
            'off_limit' => $request->get('off_limit')
        ]);

        event(new TrackKpiUpdatesEvent($kpi->id));

        return true;
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     * @param KpiUpdateRequest $request
     * @return array
     * @throws Exception|Throwable
     */
    public function update(KpiUpdateRequest $request): array
    {
        $id = $request->get('id');

        DB::beginTransaction();
        $kpi_item_ids = $this->updateItems($id, $request->get('items'));

        $all = $request->all();

        $all['updated_by'] = auth()->id();
        $all['children'] = $kpi_item_ids;

        unset($all['source']);

        $kpi = Kpi::query()->findOrFail($id);
        $kpi->update($all);
        $kpi->updated_at = now();
        $kpi->save();
        DB::commit();

        $kpiCreatedDate = Carbon::createFromFormat('Y-m-d', $kpi->created_at->format('Y-m-d'));
        event(new KpiChangedEvent($kpiCreatedDate));
        event(new TrackKpiUpdatesEvent($id));

        return [
            'id' => $id,
            'items' => $kpi_item_ids
        ];

    }

    /**
     * Сохраняем или редактируем kpi_items и возвращаем массив с id
     */
    private function updateItems(int $id, array $items): array
    {
        $item_ids = [];

        $kpi = Kpi::query()
            ->findOrFail($id);

        foreach ($items as $item) {
            $item['kpi_id'] = $id;


            unset($item['group_id']);
            unset($item['sum']);
            unset($item['percent']);

            // if a source is no_source
            // then activity also zeroes
            if ($item['source'] == 0) $item['activity_id'] = 0;

            unset($item['source']);


            if ($item['id'] == 0) {

                /**
                 * Создаем новый kpi item, потому что id == 0
                 */
                $item_ids[] = KpiItem::query()->create($item)->getKey();

            } else {

                $item_ids[] = $item['id'];

                if (isset($item['deleted'])) {
                    $kpi->items()->where('id', $item['id'])->delete();
                } else {
                    unset($item['daily_plan']);
                    unset($item['histories']);
//                    $kpi->items()->where('id', $item['id'])->update($item); move this to histories
                }

                event(new TrackKpiItemEvent($item));
            }
        }
        $kpi->updated_at = now();
        $kpi->save();
        return $item_ids;
    }

    /**
     * @param $id
     * @return void
     */
    public function delete($id): void
    {
        $kpi = Kpi::query()->find($id);
        if ($kpi) {
            $kpi->updated_by = auth()->id();
            $kpi->save();
            $kpi->delete();
        }
    }

    /**
     * @throws Throwable
     * @throws TargetDuplicateException
     */
    public function save(KpiSaveRequest $request): array
    {
//        if ($this->hasDuplicate($request)) {
//            throw new TargetDuplicateException();
//        }

        $kpi_id = 0;
        $kpi_item_ids = [];
        try {
            DB::transaction(function () use ($request, &$kpi_item_ids, &$kpi_id) {
                /** @var Kpi $kpi */
                $kpi = Kpi::query()->create([
                    'completed_80' => $request->input('completed_80'),
                    'completed_100' => $request->input('completed_100'),
                    'lower_limit' => $request->input('lower_limit'),
                    'upper_limit' => $request->input('upper_limit'),
                    'colors' => json_encode($request->input('colors')),
                    'created_by' => auth()->id()
                ]);
                foreach ($request->get('kpiables') as $kpiable) {
                    $kpi->saveTarget($kpiable);
                }

                $kpi_item_ids = $this->saveItems($request->get('items'), $kpi->id);

                $kpi->children = $kpi_item_ids;
                $kpi->updated_at = now();
                $kpi->save();

                $kpi_id = $kpi->id;
            });

            event(new TrackKpiUpdatesEvent($kpi_id));
            event(new KpiChangedEvent(Carbon::now()));

            return [
                'id' => $kpi_id,
                'items' => $kpi_item_ids
            ];
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Сохраняем kpi_items и возвращаем массив с id
     */
    private function saveItems(array $items, int $kpiId): array
    {
        $ids = [];
        foreach ($items as $item) {
            // if a source is no_source
            // then activity also zeroes
            if ($item['source'] == 0) $item['activity_id'] = 0;


            // create
            $kpi_item = KpiItem::query()->create([
                'kpi_id' => $kpiId,
                'name' => $item['name'],
                'method' => $item['method'],
                'unit' => $item['unit'] ?? '',
                'plan' => $item['plan'],
                'cell' => $item['cell'],
                'share' => $item['share'],
                'activity_id' => $item['activity_id'],
                'common' => $item['common']
            ]);

            event(new TrackKpiItemEvent($kpi_item->toArray()));

            $ids[] = $kpi_item->getKey();
        }

        return $ids;
    }

    /**
     *  Уже назначен на эту цель kpi
     * @param Request $request
     * @return bool
     */
    private function hasDuplicate(Request $request): bool
    {
        return Kpi::query()
            ->where([
                'targetable_id' => $request->get('targetable_id'),
                'targetable_type' => $request->get('targetable_type'),
            ])
            ->exists();
    }
}
