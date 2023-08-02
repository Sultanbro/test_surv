<?php

namespace App\Http\Controllers\Api\Coordinate;

use App\DTO\Coordinate\CoordinateDTO;
use App\Http\Controllers\Controller;
use App\Service\Coordinate\CoordinateService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class CoordinateController extends Controller
{
    protected $coordinateService;

    /**
     * @param CoordinateService $coordinateService
     */
    public function __construct(CoordinateService $coordinateService)
    {
        $this->coordinateService = $coordinateService;
    }

    /**
     * @return JsonResponse
     */
    public function index():JsonResponse
    {
        $coordinates = $this->coordinateService->getAllCoordinates();
        return response()->json([
            "message" => "success",
            "data"   => $coordinates
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        try {
            $dto = new CoordinateDTO($request->all());
            $coordinate = $this->coordinateService->createCoordinate($dto);
            return response()->json([
                "message" => "Created success!",
                "data"   => $coordinate
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'error',
                'errors' => $e->errors()]);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id):JsonResponse
    {
        $coordinate = $this->coordinateService->getCoordinateById($id);
        if (!$coordinate) {
            return response()->json(['error' => 'Coordinate not found']);
        }
        return response()->json([
            "message" => "success",
            "data"   => $coordinate
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id):JsonResponse
    {
        try {
            $dto = new CoordinateDTO($request->all());
            $coordinate = $this->coordinateService->updateCoordinate($id, $dto);
            return response()->json([
                "message" => "success",
                "data"   => $coordinate
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()]);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id):JsonResponse
    {
        $this->coordinateService->deleteCoordinate($id);
        return response()->json([
            'message' => 'deleted success'
        ]);
    }
}
