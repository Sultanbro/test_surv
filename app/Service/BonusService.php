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
            'bonuses'       => Bonus::query()->findOrFail($id),
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
            'bonuses'       => Bonus::get(),
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новый бонус.
     */
    public function save(BonusSaveRequest $request): void
    {
        try {
            $model = $this->getModel($request->input('targetable_type'));

            Bonus::query()->create([
                'targetable_id'     => $request->targetable_id,
                'targetable_type'   => $model,
                'title'     => $request->title,
                'sum'     => $request->sum,
                'group_id'     => $request->group_id,
                'activity_id'     => $request->activity_id,
                'unit'     => $request->unit,
                'quantity'     => $request->quantity,
                'daypart'     => $request->daypart,
                'text'     => $request->text,
            ]);
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     */
    public function update(BonusUpdateRequest $request): void
    {
        try {

            $id = $request->input('id');

            event(new BonusUpdated($id));

            Bonus::query()->findOrFail($id)->update($request->all());

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