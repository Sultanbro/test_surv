<?php

namespace App\Http\Controllers\Admin\TimeTrack;

use App\Enums\TimeTrackSettingEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTrack\DeleteSettingRequest;
use App\Http\Requests\TimeTrack\GetSettingRequest;
use App\Http\Requests\TimeTrack\StoreSettingRequest;
use App\Repositories\PositionRepository;
use App\Service\Timetrack\SettingService as TimeTrackSetting;
use Throwable;

class SettingController extends Controller
{
    public function __construct(
        public PositionRepository $positionRepository
    )
    {
//        $this->middleware('auth');
    }

    /**
     * @param GetSettingRequest $request
     * @param TimeTrackSetting $service
     * @return mixed
     * @throws Throwable
     */
    public function setting(GetSettingRequest $request, TimeTrackSetting $service)
    {
        $type = TimeTrackSettingEnum::TABS[$request->toDto()->tab];
        $response = $service->handle($type);

        return response()->success($response);
    }

    /**
     * @param StoreSettingRequest $request
     * @return mixed
     */
    public function create(StoreSettingRequest $request)
    {
        $response = $this->positionRepository->createPosition($request->toDto()->position);
        return response()->success($response);
    }

    /**
     * @param DeleteSettingRequest $request
     * @return mixed
     */
    public function delete(DeleteSettingRequest $request)
    {
        $response = $this->positionRepository->deletePosition($request->toDto()->positionId);
        return response()->success($response);
    }
}