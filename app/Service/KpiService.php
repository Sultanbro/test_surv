<?php

namespace App\Service;

use App\Events\TrackKpiUpdatesEvent;
use App\Http\Requests\KpiSaveRequest;
use App\Http\Requests\KpiUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Kpi\Kpi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KpiService
{
    const USER_ID          = 1;
    const PROFILE_GROUP_ID = 2;
    const POSITION_ID      = 3;


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
     * Сохраняем новый KPI.
     * @param KpiSaveRequest $request
     * @return void
     * @throws Exception
     */
    public function save(KpiSaveRequest $request): void
    {
        try {
            $model = $this->getModel($request->input('targetableType'));

            Kpi::query()->create([
                'targetable_id'     => $request->input('targetableId'),
                'targetable_type'   => $model,
                'completed_80'      => $request->input('completed_80'),
                'completed_100'     => $request->input('completed_100'),
                'lower_limit'       => $request->input('lower_limit'),
                'upper_limit'       => $request->input('upper_limit'),
                'colors'            => json_encode($request->input('colors'))
            ]);
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

            $id = $request->input('kpi_id');

            event(new TrackKpiUpdatesEvent($id));

            Kpi::query()->findOrFail($id)->update($request->all());

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

    private function getModel(int $targetId)
    {
        switch ($targetId) {
            case $targetId == self::USER_ID:
                return 'App\User';
            case $targetId == self::PROFILE_GROUP_ID:
                return 'App\ProfileGroup';
            case $targetId == self::POSITION_ID:
                return 'App\Position';
        }
    }
}