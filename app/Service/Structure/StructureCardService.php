<?php
declare(strict_types=1);

namespace App\Service\Structure;

use App\Models\Structure\StructureCard;
use App\Models\Structure\StructureCardManager;
use App\Models\Structure\StructureCardUser;
use App\Repositories\Structure\StructureCardRepository;
use Illuminate\Database\Eloquent\Model;

/**
* Класс для работы с Service.
*/
class StructureCardService
{
    private StructureCardRepository $structureCardRepository;

    public function __construct(StructureCardRepository $structureCardRepository)
    {
        $this->structureCardRepository = $structureCardRepository;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createStructureCard(array $data):mixed
    {
        return $this->structureCardRepository->create($data);
    }

    /**
     * @param $userId
     * @param $cardId
     * @return Model
     */
    public function createStructureCardUser($userId, $cardId):Model
    {
        return StructureCardUser::query()->create([
            'user_id' => $userId,
            'card_id' => $cardId,
        ]);
    }

    /**
     * @param $userId
     * @param $cardId
     * @param $positionId
     * @return mixed
     */
    public function createStructureCardManager($userId, $cardId, $positionId):mixed
    {
        return StructureCardManager::query()->create([
            'user_id' => $userId,
            'card_id' => $cardId,
            'position_id' => $positionId,
        ]);
    }

    /**
     * @param $managerId
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function getStructureCardWithChildrenByManager()
    {

        $structureCard = StructureCard::query()
            ->whereNull('parent_id')
            ->with('childrens', 'users:id', 'manager')
            ->firstOrFail();

        if (!$structureCard) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        return response()->json(['structure_card' => $structureCard], 200)->getData();
    }

    /**
     * @param StructureCard $structureCard
     * @param array $data
     * @return void
     */
    public function updateStructureCard(StructureCard $structureCard, array $data):void
    {

        $structureCard->update($data);

        // Update manager in structure_card_manager table
        $positionId = $data['position_id'];

        $managerId = $data['manager_id'];

        $structureCardManager = StructureCardManager::where('card_id', $structureCard->id)->first();

        if(!$structureCardManager)
        {
            StructureCardManager::query()->create([
                "user_id" => $managerId,
                "position_id" => $positionId,
                "card_id" => $structureCard->id
            ]);
        }else {
            $structureCardManager->update([
                'user_id' => $managerId,
                'position_id' => $positionId,
            ]);
        }
        if (isset($data['user_ids'])) {
            $structureCard->users()->sync($data['user_ids']);
        }
    }
}