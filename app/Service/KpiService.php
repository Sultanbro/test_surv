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
use App\ProfileGroup;
use App\Traits\KpiHelperTrait;
use Carbon\Carbon;
use Exception;
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

        $date = $filters['date'] ?? [
            'year' => now()->year,
            'month' => now()->month,
        ];

        $date = Carbon::createFromDate($date['year'], $date['month']);
        $endOfDate = $date->endOfMonth()->format('Y-m-d');
        $startOfDate = $date->startOfMonth()->format('Y-m-d');

        $kpis = Kpi::query()
            ->when($searchWord, fn() => (new KpiFilter)->globalSearch($searchWord))
            ->with([
                    'items' => function (HasMany $query) use ($endOfDate, $startOfDate) {
                        $query->with(['histories' => function (MorphMany $query) use ($endOfDate, $startOfDate) {
                            $query->whereDate('created_at', '>=', $startOfDate);
                        }]);
                    },
                    'user' => fn(HasOne $query) => $query->select('id'),
                    'user.groups' => fn(BelongsToMany $query) => $query->select('name')->where('status', 'active'),
                    'creator',
                    'updater',
                    'histories' => function (morphMany $query) use ($startOfDate) {
                        $query->whereDate('created_at', '>=', $startOfDate);
                    }
                ]
            )->get();
        $kpis_final = [];

        foreach ($kpis as $kpi) {

            $item = $kpi->toArray();

            // remove items if it's not in history
            if ($kpi->histories->first()) {
                $payload = json_decode($kpi->histories->first()->payload, true);

                $items = $kpi->items->where('created_at', '>=', $startOfDate);

                if (isset($payload['children'])) {
                    $items = $items->whereIn('id', $payload['children'])->where('created_at', '>=', $startOfDate);
                }

                foreach ($items as $_item) {

                    $history = $_item->histories->first();

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
     * @throws Throwable
     * @throws TargetDuplicateException
     */
    public function save(KpiSaveRequest $request): array
    {
        if ($this->hasDuplicate($request)) {
            throw new TargetDuplicateException();
        }

        $kpi_id = 0;
        $kpi_item_ids = [];

        try {
            DB::transaction(function () use ($request, &$kpi_item_ids, &$kpi_id) {
                $kpi = Kpi::query()->create([
                    'targetable_id' => $request->input('targetable_id'),
                    'targetable_type' => $request->input('targetable_type'),
                    'completed_80' => $request->input('completed_80'),
                    'completed_100' => $request->input('completed_100'),
                    'lower_limit' => $request->input('lower_limit'),
                    'upper_limit' => $request->input('upper_limit'),
                    'colors' => json_encode($request->input('colors')),
                    'created_by' => auth()->id()
                ]);

                $kpi_item_ids = $this->saveItems($request->get('items'), $kpi->id);

                $kpi->children = $kpi_item_ids;
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
     * @param Request $request
     * @return void
     */
    public function delete(Request $request): void
    {
        $kpi = Kpi::query()->find($request->get('id'));

        if ($kpi) {
            $kpi->updated_by = auth()->id();
            $kpi->save();
            $kpi->delete();
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

            event(new TrackKpiItemEvent($kpi_item->getKey()));

            $ids[] = $kpi_item->getKey();
        }

        return $ids;
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
                    $kpi->items()->where('id', $item['id'])->update($item);
                }

                event(new TrackKpiItemEvent($item['id']));
            }
        }

        return $item_ids;
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