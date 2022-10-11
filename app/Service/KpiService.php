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
use App\Exceptions\Kpi\TargetDuplicateException;
use Carbon\Carbon;

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
            'groups'     => \App\ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
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
        
        $last_date = Carbon::now()->endOfMonth()->format('Y-m-d');

        $kpis = Kpi::with([
            'items',
            'creator',
            'updater',
            'histories' => function($query) use ($last_date) {
                    $query->whereDate('created_at', '<=', $last_date);
            },
            'items.histories' => function($query) use ($last_date) {
                $query->whereDate('created_at', '<=', $last_date);
            },
        ])->get();
        
        $kpis_final = [];

        foreach ($kpis as $key => $kpi) {

            $item = $kpi->toArray();

           
            // remove items if it's not in history
            if($kpi->histories->first()) {
                $payload = json_decode($kpi->histories->first()->payload, true);
                
                $items = $kpi->items;

                if(isset($payload['children'])) {
                    $items = $kpi->items->whereIn('id', $payload['children']);
                } 

                foreach ($items as $key => $_item) {

                    $history = $_item->histories->first();

                    $has_edited_plan = $history ? json_decode($history->payload, true) : false;
                    
                    $_item['daily_plan'] = $has_edited_plan && array_key_exists('plan', $has_edited_plan)
                        ? $has_edited_plan['plan']
                        : (float)$_item->plan;

                    $_item['plan'] = $_item['daily_plan'];
                }

                $item['items'] = $items->values();
            }

            array_push($kpis_final, $item);
        }
        

        return [
            'kpis'       => $kpis_final,
            'activities' => Activity::get(),
            'groups' => \App\ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
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
        if($this->hasDuplicate($request)) {
            throw new TargetDuplicateException();
        }

        $kpi_id = 0;
        $kpi_item_ids = [];

        try {
            DB::transaction(function () use ($request, &$kpi_item_ids, &$kpi_id){
                $kpi = Kpi::query()->create([
                    'targetable_id'     => $request->input('targetable_id'),
                    'targetable_type'   => $request->targetable_type,
                    'completed_80'      => $request->input('completed_80'),
                    'completed_100'     => $request->input('completed_100'),
                    'lower_limit'       => $request->input('lower_limit'),
                    'upper_limit'       => $request->input('upper_limit'),
                    'colors'            => json_encode($request->input('colors')),
                    'created_by'        => auth()->id()
                ]);

                $kpi_item_ids = $this->saveItems($request->items, $kpi->id);

                $kpi->children = $kpi_item_ids;
                $kpi->save();

                $kpi_id = $kpi->id;

            });

            event(new TrackKpiUpdatesEvent($kpi_id));

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
     * @return void
     * @throws Exception
     */
    public function update(KpiUpdateRequest $request): array
    {
            $id = $request->id;
            $kpi_item_ids = [];
     
            DB::transaction(function () use ($request, $id, &$kpi_item_ids) {

                $kpi_item_ids = $this->updateItems($id, $request->items);
                
                $all = $request->all();

                $all['updated_by'] = auth()->id();
                $all['children'] = $kpi_item_ids;

                unset($all['source']);

                Kpi::findOrFail($id)->update($all);
            });

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
        $kpi = Kpi::find($request->id);

        if($kpi) {
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
        foreach ($items as $item)
        {
            // if source is no_source 
            // then activity also zero
            if($item['source'] == 0) $item['activity_id'] = 0;


            // create
            $kpi_item = KpiItem::create([
                'kpi_id'        => $kpiId,
                'name'          => $item['name'],
                'method'        => $item['method'],
                'unit'          => isset($item['unit']) ? $item['unit'] : '',
                'plan'          => $item['plan'],
                'cell'          => $item['cell'],
                'share'         => $item['share'],
                'activity_id'   => $item['activity_id'],
                'common'        => $item['common']
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

        $kpi = Kpi::findOrFail($id);
        
        foreach ($items as $item)
        {
            $item['kpi_id'] = $id;
            
           
            unset($item['group_id']);
            unset($item['sum']);
            unset($item['percent']);
            
            // if source is no_source 
            // then activity also zero
            if($item['source'] == 0) $item['activity_id'] = 0;

            unset($item['source']);

            
            if($item['id'] == 0) {

                /**
                 * Создаем новый kpi item, потому что id == 0
                 */
                $item_ids[] = KpiItem::create($item)->id;

            } else {

                $item_ids[] = $item['id'];

                /**
                 * Обновляем kpi_item
                 */
                
                if (isset($item['deleted'])) {
                    $kpi->items()->where('id', $item['id'])->delete();
                }else{
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