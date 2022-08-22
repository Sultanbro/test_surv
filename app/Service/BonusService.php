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
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить бонусы и активности и группы.
     * @param array $filters
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        return [
            'bonuses'    => Bonus::with('creator', 'updater')->get(),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новый бонус.
     */
    public function save(BonusSaveRequest $request): array
    {
        try {
            $bonus = Bonus::create([
                'targetable_id'     => $request->targetable_id,
                'targetable_type'   => $request->targetable_type,
                'title'     => $request->title,
                'sum'     => $request->sum,
                'group_id'     => $request->group_id,
                'activity_id'     => $request->activity_id,
                'unit'     => $request->unit,
                'quantity'     => $request->quantity,
                'daypart'     => $request->daypart,
                'text'     => $request->text,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
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

            $id = $request->input('id');

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
        Bonus::find($request->id)->delete();
    }
}