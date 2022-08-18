<?php

namespace App\Service;

use App\Events\ActivityUpdated;
use App\Http\Requests\ActivitySaveRequest;
use App\Http\Requests\ActivityUpdateRequest;
use App\Models\Analytics\Activity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\ProfileGroup;

class ActivityService
{
    /**
     * Получить активность и группы.
     * @param int $id
     */
    public function get(int $id): array
    {
        return [
            'activities' => Activity::witTrashed()->find($id),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Получить активности и группы.
     * @param array $filters
     */
    public function fetch($filters): array
    {   
        if($filters !== null) {} 
        
        return [
            'activities' => Activity::get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новую активность.
     */
    public function save(ActivitySaveRequest $request): void
    {
        try {

            Activity::query()->create([
                'name'     => $request->targetable_id,
                'group_id'     => $request->group_id,
                'daily_plan'     => $request->daily_plan,
                'unit'     => $request->unit,
                'share'     => $request->share,
                'method'     => $request->method,
                'view'     => $request->view,
                'source'     => $request->source,
                'editable'     => $request->editable,
                'order'     => $request->order,
                'type'     => $request->type,
                'weekdays'     => $request->weekdays,
            ]);
            
        } catch (Exception $exception) {
            throw new Exception($exception);
        }
    }

    /**
     * Обновляем данные и сохраняем в histories старые данные.
     */
    public function update(ActivityUpdateRequest $request): void
    {
        try {

            $id = $request->input('id');

            event(new ActivityUpdated($id));

            Activity::query()->findOrFail($id)->update($request->all());

        } catch (Exception $exception){
            Log::error($exception);
            throw new Exception($exception);
        }
    }

    /**
     * Удалить активность
     */
    public function delete(Request $request): void
    {
        Activity::find($request->id)->delete();
    }
}