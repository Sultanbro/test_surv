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
use App\Models\Kpi\Builder\KpiSearch;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\ReadModels\KpiReadModel;
use App\Traits\KpiHelperTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiService
{
    use KpiHelperTrait;

    /**
     * Получить kpis, activities.
     * @param int $id
     * @return array
     */
    public function get(int $id): array
    {
        $kpi = Kpi::query()->findOrFail($id)->items();

        return [
            'kpis' => $kpi->get(),
            'activities' => Activity::get(),
            'groups' => \App\ProfileGroup::where('active', 1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить kpis, activities. по фильтру
     * @param int $id
     * @return array
     */
    public function fetch($filters): array
    {
        if ($filters !== null) {
        }

        $searchWord = $filters['query'] ?? null;

        $date = $filters['date'] ?? null;

        $carbon = isset($date) ? Carbon::createFromDate($date['year'], $date['month']) : Carbon::now();

        $last_date = $carbon->endOfMonth()->format('Y-m-d');

        $kpis = Kpi::query()
            ->when($searchWord, fn() => (new KpiFilter)->globalSearch($searchWord))
            ->with([
                'items' => function ($query) use ($last_date) {
                    $query->withTrashed()->whereDate('created_at', '<=', $last_date);
                },
                'user' => fn($query) => $query->select('id'),
                'user.groups' => fn($query) => $query->select('name')->where('status', 'active'),
                'creator',
                'updater',
                'histories' => function ($query) use ($last_date) {
                    $query->whereDate('created_at', '<=', $last_date);
                },
                'items.histories' => function ($query) use ($last_date) {
                    $query->whereDate('created_at', '<=', $last_date);
                },
            ])->get();
        $kpis_final = [];

        foreach ($kpis as $key => $kpi) {

            $item = $kpi->toArray();


            // remove items if it's not in history
            if ($kpi->histories->first()) {
                $payload = json_decode($kpi->histories->first()->payload, true);

                $items = $kpi->items->whereNull('deleted_at');

                if (isset($payload['children'])) {
                    $items = $items->whereIn('id', $payload['children']);
                }

                foreach ($items as $key => $_item) {

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

            array_push($kpis_final, $item);
        }


        return [
            'kpis' => $kpis_final,
            'activities' => Activity::get(),
            'groups' => \App\ProfileGroup::where('active', 1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новый KPI.
     * @param KpiSaveRequest $request
     * @return void
     * @throws Exception
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
                    'targetable_type' => $request->targetable_type,
                    'completed_80' => $request->input('completed_80'),
                    'completed_100' => $request->input('completed_100'),
                    'lower_limit' => $request->input('lower_limit'),
                    'upper_limit' => $request->input('upper_limit'),
                    'colors' => json_encode($request->input('colors')),
                    'created_by' => auth()->id()
                ]);

                $kpi_item_ids = $this->saveItems($request->items, $kpi->id);

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
     * @throws Exception
     */
    public function update(KpiUpdateRequest $request): array
    {
        $id = $request->id;
        $kpi_item_ids = [];
        $kpi = null;

        DB::transaction(function () use ($request, $id, &$kpi_item_ids, &$kpi) {

            $kpi_item_ids = $this->updateItems($id, $request->items);

            $all = $request->all();

            $all['updated_by'] = auth()->id();
            $all['children'] = $kpi_item_ids;

            unset($all['source']);

            $kpi = Kpi::findOrFail($id);
            $kpi->update($all);
        });

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
        $kpi = Kpi::query()->find($request->id);

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
            // if source is no_source 
            // then activity also zero
            if ($item['source'] == 0) $item['activity_id'] = 0;


            // create
            $kpi_item = KpiItem::create([
                'kpi_id' => $kpiId,
                'name' => $item['name'],
                'method' => $item['method'],
                'unit' => isset($item['unit']) ? $item['unit'] : '',
                'plan' => $item['plan'],
                'cell' => $item['cell'],
                'share' => $item['share'],
                'activity_id' => $item['activity_id'],
                'common' => $item['common']
            ]);

            event(new TrackKpiItemEvent($kpi_item->id));

            $ids[] = $kpi_item->id;
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

            // if source is no_source 
            // then activity also zero
            if ($item['source'] == 0) $item['activity_id'] = 0;

            unset($item['source']);


            if ($item['id'] == 0) {

                /**
                 * Создаем новый kpi item, потому что id == 0
                 */
                $item_ids[] = KpiItem::query()->create($item)->getKey();

            } else {

                $item_ids[] = $item['id'];

                /**
                 * Обновляем kpi_item
                 */

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
     * @param array $filters
     */
    private function hasDuplicate(Request $request): bool
    {
        return Kpi::where([
            'targetable_id' => $request->targetable_id,
            'targetable_type' => $request->targetable_type,
        ])->exists();
    }
}