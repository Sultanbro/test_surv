<?php

namespace App\Http\Controllers\Admin\TimeTrack;

use App\Enums\TimeTrackSettingEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTrackSettingRequest;
use App\Service\Timetrack\SettingService as TimeTrackSetting;
use App\Helpers\TimeTrackSetting as Helper;

class TimeTrackSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function setting(TimeTrackSettingRequest $request, TimeTrackSetting $service)
    {
        $type = TimeTrackSettingEnum::TABS[$request->toDto()->tab];
        $response = $service->handle($type);

        return response()->success($response);
    }
}
