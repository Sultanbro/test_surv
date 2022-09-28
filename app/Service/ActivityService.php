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
            'items'      => Activity::withTrashed()->with('creator', 'updater')->find($id),
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
            'items'      => Activity::with('creator', 'updater')->get(),
            'groups'     => ProfileGroup::get()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Сохраняем новую активность.
     */
    public function save(ActivitySaveRequest $request): array
    {
        try {

            $activity = Activity::query()->create([
                'name'     => $request->name,
                'group_id'     => $request->group_id,
                'daily_plan'     => $request->daily_plan,
                'unit'     => $request->unit,
                'share'     => 0,
                'type'     => 0,
                'plan_unit'     => 0,
                'plan_type'     => 0,
                'method'     => $request->method,
                'view'     => $request->view,
                'source'     => $request->source,
                'editable'     => $request->editable,
                'order'     => $request->order,
                'weekdays'     => $request->weekdays,
                'created_by' => auth()->id()
            ]);

            return [
                'indicator' => $activity
            ];
            
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

            $id = $request->id;
            $all = $request->all();
            $all['updated_by'] = auth()->id();

            event(new ActivityUpdated($id));

            Activity::query()->findOrFail($id)->update($all);

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