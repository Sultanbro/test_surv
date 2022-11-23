<?php

namespace App\Http\Controllers\Kpi;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonusSaveRequest;
use App\Http\Requests\BonusUpdateRequest;
use App\Models\Kpi\Bonus;
use App\Service\BonusService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use App\ProfileGroup;
use App\Position;
use App\Models\Analytics\Activity;
use App\Service\GroupUserService;

class BonusController extends Controller
{
    protected $bonusService;

    public function __construct(BonusService $bonusService)
    {
        $this->bonusService = $bonusService;

    }

    public function get(Request $request)
    {
        $response = $this->bonusService->fetch($request->filters);

        return response()->json($response);
    }

    public function getUserBonuses()
    {
        $response = $this->bonusService->getUserBonuses(Auth::id());

        return response()->json($response);
    }

    /**
     * Сохранение
     */
    public function save(Request $request): JsonResponse
    {
        $data = $request->only([
            'title',
            'sum',
            'group_id',
            'activity_id',
            'unit',
            'quantity',
            'daypart',
            'text',
            'targetable_id',
            'targetable_type'
        ]);

        $response = $this->bonusService->save($data);

        return response()->json($response);
    }

    /**
     * Обновление
     */
    public function update(BonusUpdateRequest $request): JsonResponse
    {
        $response = $this->bonusService->update($request);

        return response()->json($response);
    }

    /**
     * Удаление
     */
    public function delete(int $id): JsonResponse
    {
        $response = $this->bonusService->delete($id);

        return response()->json($response);
    }
}
