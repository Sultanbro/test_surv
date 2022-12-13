<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\TimeTrack;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeTrack\GetReportRequest;
use App\Repositories\ProfileGroupRepository;
use App\Service\Timetrack\ReportService as TimeTrackReport;
use App\User;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final class ReportController extends Controller
{
    public function __construct(
        public TimeTrackReport $reportService
    )
    {}

    public function getEnter()
    {
        $user = auth()->user();
        abort_if(!$user->can('entertime_view'), Response::HTTP_FORBIDDEN, "You don't have permission!");

        View::share('title', 'Время прихода');
        View::share('menu', 'timetrackingenters');

        $groups = $this->reportService->get($user)['groups'];
        $years  = $this->reportService->get($user)['years'];

        return view('admin.enter-report', compact('groups', 'years'));
    }

    /**
     *  @Post {
     *  "group_id": 23,
     *  "year": 2022,
     *  "month": 6,
     *  "day": 2
     * }
     */
    public function enter(GetReportRequest $request)
    {
        $user = auth()->user() ?? User::query()->findOrFail(5);
        abort_if(!$user->can('entertime_view'), Response::HTTP_FORBIDDEN, "You don't have permission!");

        $response = $this->reportService->post(
            $request->toDto()->groupId,
            $request->toDto()->year,
            $request->toDto()->month,
            $request->toDto()->day
        );

        return \response()->success($response);
    }
}