<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\Cells\SaveCellActivityRequest;
use App\Service\Analytics\Cells\CellActivityService;
use Illuminate\Http\JsonResponse;

class CellController extends Controller
{

    public function saveCellActivity(SaveCellActivityRequest $request, CellActivityService $service): JsonResponse
    {
        $response = $service->handle($request->toDto());

        return $this->response(
            message: 'Success',
            data: $response
        );
    }

    public function saveCellTime()
    {
        //
    }

    public function saveCellSum()
    {
        //
    }

    public function saveCellAvg()
    {
        //
    }
}
