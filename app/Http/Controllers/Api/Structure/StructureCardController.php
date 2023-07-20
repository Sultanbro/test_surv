<?php

namespace App\Http\Controllers\Api\Structure;

use App\DTO\Structure\StructureCardDTO;
use App\DTO\Structure\StructureCardUpdateDTO;
use App\Http\Controllers\Controller;
use App\Models\Structure\StructureCard;
use App\Service\Structure\StructureCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StructureCardController extends Controller
{
    private StructureCardService $structureCardService;

    public function __construct(StructureCardService $structureCardService)
    {
        $this->structureCardService = $structureCardService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request):JsonResponse
    {
        $data = StructureCardDTO::validate($request);

        $structureCard = $this->structureCardService->createStructureCard($data);

        $userIds = [];
        if ($data['user_ids'])
        {
            foreach ($data['user_ids'] as $userId) {
                $structureCardUser = $this->structureCardService->createStructureCardUser($userId, $structureCard->id);
                $userIds[] = $structureCardUser->user_id;
            }
        }
        $structureCardManager = $this->structureCardService->createStructureCardManager($data['manager_id'], $structureCard->id, $data['position_id']);

        return response()->json([
            'structure_card' => $structureCard,
            'structure_card_user' => $userIds,
            'structure_card_manager' => $structureCardManager,
        ], 201);
    }


    /**
     * @param $userId
     * @return JsonResponse
     */
    public function all():JsonResponse
    {
        $managerId = 5;
        $structureCard = $this->structureCardService->getStructureCardWithChildrenByManager($managerId);

        return response()->json($structureCard);
    }

    /**
     * @param StructureCardUpdateDTO $request
     * @param StructureCard $structureCard
     * @return JsonResponse
     */
    public function update(StructureCardUpdateDTO $request, StructureCard $structureCard):JsonResponse
    {
        $validatedData = $request->validated();

        $this->structureCardService->updateStructureCard($structureCard, $validatedData);

        return response()->json([
            'message' => 'Structure card updated successfully.',
        ]);
    }

    /**
     * @param $cardId
     * @return JsonResponse
     */
    public function destroy($cardId):JsonResponse
    {
        $card = StructureCard::query()->findOrFail($cardId)->delete();

        return response()->json([
            "data" => $card,
            'message' => 'Card and related records deleted successfully'
        ]);

    }
}
