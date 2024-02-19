<?php

namespace App\Service\Tax;

use App\DTO\Tax\GetTaxesResponseDTO;
use App\DTO\Tax\TaxGroupDTO;
use App\DTO\Tax\SetUserTaxDTO;
use App\Events\TrackTaxGroupItemEvent;
use App\Models\TaxGroup;
use App\Models\TaxGroupItem;
use App\Models\UserTax;
use App\Repositories\TaxRepository;

/**
* Класс для работы с Service.
*/
class TaxesService
{

    public function getAll()
    {
        return TaxGroup::with('items')->get();
    }

    public function getUserTaxes($userId)
    {
        return UserTax::with('taxGroup.items')
            ->where('status', UserTax::ACTIVE)
            ->where('user_id', $userId)
            ->first()?->taxGroup;
    }

    public function getOne($id)
    {
        return TaxGroup::with('items')->where('id', $id)->firstOrFail();
    }

    public function create(TaxGroupDTO $dto): TaxGroup
    {
        /** @var TaxGroup $taxGroup */
        $taxGroup = TaxGroup::query()->create([
            'name' => $dto->name
        ]);

        $this->saveItems($taxGroup->id, $dto->items);

        $taxGroup->load('items');

        return $taxGroup;
    }

    public function update(TaxGroupDTO $dto, int $id): TaxGroup
    {
        /** @var TaxGroup $taxGroup */
        $taxGroup = TaxGroup::query()->findOrFail($id);
        $taxGroup->update(['name' => $dto->name]);

        $taxGroup->items()->delete();
        $this->saveItems($taxGroup->id, $dto->items);

        $taxGroup->load('items');
        return $taxGroup;
    }

    public function delete(int $id): bool
    {
        /** @var TaxGroup $taxGroup */
        $taxGroup = TaxGroup::query()->findOrFail($id);
        $taxGroup->delete();
        return true;
    }

    protected function saveItems($taxGroupId, $items): void
    {
        foreach ($items as $item) {
            $taxItem = TaxGroupItem::query()->create([
                'tax_group_id' => $taxGroupId,
                'name' => $item['name'],
                'is_percent' => $item['is_percent'],
                'end_subtraction' => $item['end_subtraction'],
                'value' => $item['value'],
                'order' => $item['order'],
                'is_deduction' => $item['is_deduction'] ?? 0
            ]);

            event(new TrackTaxGroupItemEvent($taxItem->toArray()));
        }
    }

    public function setAssigned(SetUserTaxDTO $dto): bool
    {
        if ($dto->assigned) {
            $this->attach($dto);
        } else {
            $this->detach($dto);
        }

        return true;
    }
    public function attach(SetUserTaxDTO $dto): void
    {
        $exist = UserTax::query()
            ->where('user_id', $dto->userId)
            ->where('tax_group_id', $dto->taxGroupId)
            ->whereNull('to')
            ->exists();

        if ($exist) {
            UserTax::query()
                ->where('user_id', $dto->userId)
                ->where('tax_group_id', $dto->taxGroupId)
                ->whereNull('to')
                ->update([
                    'status' => UserTax::REMOVED,
                    'to' => now()->toDateString()
                ]);
        }

        UserTax::query()->create([
            'user_id' => $dto->userId,
            'tax_group_id' => $dto->taxGroupId,
            'status' => UserTax::ACTIVE,
            'from' => now()->toDateString()
        ]);
    }

    public function detach(SetUserTaxDTO $dto): void
    {
        $exist = UserTax::query()
            ->where('user_id', $dto->userId)
            ->where('tax_group_id', $dto->taxGroupId)
            ->whereNull('to')
            ->exists();

        if ($exist) {
            UserTax::query()
                ->where('user_id', $dto->userId)
                ->where('tax_group_id', $dto->taxGroupId)
                ->whereNull('to')
                ->update([
                    'status' => UserTax::REMOVED,
                    'to' => now()->toDateString()
                ]);
        }
    }
}
