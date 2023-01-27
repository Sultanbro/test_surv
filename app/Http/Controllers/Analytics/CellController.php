<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\Cells\SaveCellActivityRequest;
use App\Http\Requests\Analytics\Cells\SaveCellSumAvgRequest;
use App\Http\Requests\Analytics\Cells\SaveCellTimeRequest;
use App\Service\Analytics\Cells\CellActivityService;
use App\Service\Analytics\Cells\CellAvgService;
use App\Service\Analytics\Cells\CellSumService;
use App\Service\Analytics\Cells\CellTimeService;
use Illuminate\Http\JsonResponse;

class CellController extends Controller
{
    /**
     * @OA\Post(
     *     summary="Save activity cell",
     *     path="/timetracking/analytics/save-cell-activity",
     *     description="Save activity cell"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     *
     * @param SaveCellActivityRequest $request
     * @param CellActivityService $service
     * @return JsonResponse
     */
    public function saveCellActivity(SaveCellActivityRequest $request, CellActivityService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * @OA\Post(
     *     summary="Save time cell",
     *     path="/timetracking/analytics/save-cell-time",
     *     description="Save time cell"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     *
     * @param SaveCellTimeRequest $request
     * @param CellTimeService $service
     * @return JsonResponse
     */
    public function saveCellTime(SaveCellTimeRequest $request, CellTimeService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * @OA\Post(
     *     summary="Save sum cell",
     *     path="/timetracking/analytics/save-cell-sum",
     *     description="Save sum cell"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     *
     * @param SaveCellSumAvgRequest $request
     * @param CellSumService $service
     * @return JsonResponse
     */
    public function saveCellSum(SaveCellSumAvgRequest $request, CellSumService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    /**
     * @OA\Post(
     *     summary="Save avg cell",
     *     path="/timetracking/analytics/save-cell-avg",
     *     description="Save avg cell"
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *      ),
     * )
     *
     * @param SaveCellSumAvgRequest $request
     * @param CellAvgService $service
     * @return JsonResponse
     */
    public function saveCellAvg(SaveCellSumAvgRequest $request, CellAvgService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }
}
