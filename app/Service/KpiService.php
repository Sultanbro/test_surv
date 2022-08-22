<?php

namespace App\Service;

use App\Events\TrackKpiItemEvent;
use App\Events\TrackKpiUpdatesEvent;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Kpi\Kpi;
use App\Models\Kpi\KpiItem;
use App\Traits\KpiHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'kpis'       => $kpi->get(),
            'activities' => Activity::get(),
            'groups' => \App\ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить kpis, activities. по фильтру
     * @param int $id
     * @return array
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        $kpis = Kpi::with('items')->get();

        return [
            'kpis'       => $kpis,
            'activities' => Activity::get(),
            'groups' => \App\ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новый KPI.
     * @param KpiSaveRequest $request
     * @return void
     * @throws Exception
     */
    public function save(KpiSaveRequest $request): void
    {
        if($this->hasDuplicate($request)) {
            throw new TargetDuplicateException();
        }

        try {
            DB::transaction(function () use ($request){
                $kpi = Kpi::query()->create([
                    'targetable_id'     => $request->input('targetable_id'),
                    'targetable_type'   => $request->targetable_type,
                    'completed_80'      => $request->input('completed_80'),
                    'completed_100'     => $request->input('completed_100'),
                    'lower_limit'       => $request->input('lower_limit'),
                    'upper_limit'       => $request->input('upper_limit'),
                    'colors'            => json_encode($request->input('colors'))
                ]);

                $this->saveItems((array) $request->input('items'), (int) $kpi->id);
            });
        }catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     * @param KpiUpdateRequest $request
     * @return void
     * @throws Exception
     */
    public function update(KpiUpdateRequest $request): void
    {
        try {

            if($this->hasDuplicate($request)) {
                throw new TargetDuplicateException();
            }

            $id = $request->input('kpi_id');

            event(new TrackKpiUpdatesEvent($id));

            DB::transaction(function () use ($request, $id){
                $items = $request->input('items');

                $this->updateItems($id, $items);

                Kpi::query()->findOrFail($id)->update($request->all());
            });

        }catch (Exception $exception){
            Log::error($exception);
            throw new Exception($exception);
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function delete(Request $request): void
    {
        $request->only([
            'kpi_id'
        ]);

        $id = $request->input('kpi_id');
        Kpi::query()->find($id)->delete();
    }

    /**
     * @param array $items
     * @param int $kpiId
     * @return void
     */
    private function saveItems(array $items, int $kpiId): void
    {
        foreach ($items as $item)
        {
            KpiItem::query()->create([
                'kpi_id'        => $kpiId,
                'name'          => $item['name'],
                'plan'          => $item['plan'],
                'share'         => $item['share'],
                'activity_id'   => $item['activity_id']
            ]);
        }
    }

    /**
     * @param int $id
     * @param array $items
     * @return void
     */
    private function updateItems(int $id, array $items): void
    {
        $kpi = Kpi::query()->findOrFail($id);

        foreach ($items as $item)
        {
            /*
             * Записываем в таблицу histories
             */
            event(new TrackKpiItemEvent($item['id']));

            if (isset($item['deleted'])) {
                $kpi->items()->where('id', $item['id'])->delete();
            }else{
                $kpi->items()->where('id', $item['id'])->update($item);
            }
        }
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