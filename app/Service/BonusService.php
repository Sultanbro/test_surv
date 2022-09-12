<?php

namespace App\Service;

use App\Events\BonusUpdated;
use App\Http\Requests\BonusSaveRequest;
use App\Http\Requests\BonusUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Kpi\Bonus;
use App\Traits\KpiHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;
use App\Exceptions\Kpi\TargetDuplicateException;

class BonusService
{
    use KpiHelperTrait;

    /**
     * Получить бонус и активности и группы.
     * @param int $id
     */
    public function get(int $id): array
    {
        return [
            'bonuses'    => Bonus::query()->findOrFail($id),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить бонусы и активности и группы.
     * @param array $filters
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        $bonuses = Bonus::with('creator', 'updater')->get();
 
        return [
            'bonuses'    => $this->groupItems($bonuses),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::where('active',1)->get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Группировать бонусы
     */
    private function groupItems($items) {
        $arr = [];

        $types = $items->where('target', '!=', null)->groupBy('target.type');
 
        foreach ($types as $type => $type_items) {
            foreach ($type_items->groupBy('target.name') as $name => $name_items) {
                $arr[] = [
                    'type'     => $type,
                    'name'     => $name,
                    'id'       => $name_items[0]->target['id'],
                    'items'    => $name_items,
                    'expanded' => false
                ];
            }
        }
        
        return $arr;
    }

    /**
     * Сохраняем новый бонус.
     * @throws Exception
     */
    public function save(Request $request): array
    {
        try {
            $bonus = [];
            for ($i = 0; $i < count($request->input('activity_id')); $i++)
            {
                $bonus[] = Bonus::query()->create([
                    'targetable_id'     => $request->input('targetable_id'),
                    'targetable_type'   => $this->getModel($request->input('targetable_type')),
                    'title'             => $request->input('title')[$i],
                    'sum'               => $request->input('sum')[$i],
                    'group_id'          => $request->input('group_id'),
                    'activity_id'       => $request->input('activity_id')[$i],
                    'unit'              => $request->input('unit')[$i],
                    'quantity'          => $request->input('quantity')[$i],
                    'daypart'           => $request->input('daypart')[$i],
                    'text'              => $request->input('text')[$i],
                    'created_by'        => auth()->id() ?? 5,
                    'updated_by'        => auth()->id() ?? 5,
                ]);
            }
        } catch (Exception $exception) {
            throw new Exception($exception);
        }

        return [
            'bonus' => $bonus
        ];
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     */
    public function update(BonusUpdateRequest $request): void
    {
        try {

            $id = $request->id;
            event(new BonusUpdated($id));
          
            $all = $request->all();
            $all['updated_by'] = auth()->id();

            Bonus::query()->findOrFail($id)->update($all);

        } catch (Exception $exception){
            Log::error($exception);
            
            throw new Exception($exception);
        }
    }

    /**
     * Удалить бонус
     */
    public function delete(Request $request): void
    {
        Bonus::findOrFail($request->id)->delete();
    }

    private function getData(array $data)
    {
        foreach ($data as $item)
        {
            return $item;
        }
    }
}