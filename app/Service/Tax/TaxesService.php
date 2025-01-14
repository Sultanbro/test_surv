<?php

namespace App\Service\Tax;

use App\DTO\Tax\EditUserTaxDTO;
use App\DTO\Tax\TaxGroupDTO;
use App\DTO\Tax\SetUserTaxDTO;
use App\Events\TrackTaxGroupItemEvent;
use App\Models\TaxGroup;
use App\Models\TaxGroupItem;
use App\Models\UserTax;
use App\User;
use Carbon\Carbon;

/**
 * Класс для работы с Service.
 */
class TaxesService
{

    public function getAll()
    {
        return TaxGroup::with('items')->get();
    }

    public function getUserTax($userId)
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
        $this->detachActive($dto);

        if ($dto->assigned) {
            $this->attachActive($dto);
        }

        return true;
    }

    public function attachActive(SetUserTaxDTO $dto, $reason = ''): void
    {
        /** @var User $user */
        $user = auth()->user();

        UserTax::query()->create([
            'user_id' => $dto->userId,
            'tax_group_id' => $dto->taxGroupId,
            'status' => UserTax::ACTIVE,
            'from' => now()->toDateString(),
            'payload' => json_encode([
                'editor_id' => $user->id,
                'editor_name' => $user->name . " " . $user->last_name,
                'date' => now()->format('Y-m-d'),
                'comment' => $reason,
                'action' => 'add'
            ])
        ]);
    }

    public function detachActive(SetUserTaxDTO $dto, $reason = ''): void
    {
        /** @var User $user */
        $user = auth()->user();

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
                    'to' => now()->toDateString(),
                    'payload' => json_encode([
                        'editor_id' => $user->id,
                        'editor_name' => $user->name . " " . $user->last_name,
                        'date' => now()->toDateString(),
                        'comment' => $reason,
                        'action' => 'delete'
                    ])
                ]);
        }
    }

    public function editUserTax(EditUserTaxDTO $dto)
    {
        $givenMonth = Carbon::createFromDate($dto->year, $dto->month);

        /** @var User $user */
        $user = auth()->user();
        /** @var UserTax $userTax */
        $userTax = $this->getUserTaxForGivenMonth($dto->userId, $givenMonth);

        if (!$userTax) {
            $newTax = UserTax::query()->create([
                'user_id' => $dto->userId,
                'tax_group_id' => $dto->taxGroupId,
                'status' => UserTax::REMOVED,
                'from' => (clone $givenMonth)->startOfMonth()->toDateString(),
                'to' => (clone $givenMonth)->endOfMonth()->toDateString(),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->format('Y-m-d'),
                    'comment' => $dto->reason,
                    'action' => 'edit'
                ])
            ]);
            $newTax->load('taxGroup.items');
            return $newTax;
        }

        if ($userTax->status == UserTax::ACTIVE) {
            // For previous periods
            $userTax->update([
                'status' => UserTax::REMOVED,
                'to' => (clone $givenMonth)->startOfMonth()->format('Y-m-d')
            ]);

            if ((clone $givenMonth)->startOfMonth()->isBefore(now()->startOfMonth())) {
                // For date's month
                $newTax = UserTax::query()->create([
                    'user_id' => $dto->userId,
                    'tax_group_id' => $dto->taxGroupId,
                    'status' => UserTax::REMOVED,
                    'from' => (clone $givenMonth)->format('Y-m-d'),
                    'to' => (clone $givenMonth)->endOfMonth()->format('Y-m-d'),
                    'payload' => json_encode([
                        'editor_id' => $user->id,
                        'editor_name' => $user->name . " " . $user->last_name,
                        'date' => now()->toDateString(),
                        'comment' => $dto->reason,
                        'action' => 'edit'
                    ])
                ]);
                // For next months
                UserTax::query()->create([
                    'user_id' => $dto->userId,
                    'tax_group_id' => $userTax->tax_group_id,
                    'status' => UserTax::ACTIVE,
                    'from' => (clone $givenMonth)->addMonth()->startOfMonth()->format('Y-m-d') // this considers also if there are several months between date and now
                ]);
            }
            else {
                // This works as edit in profile, because date's month is current month
                $newTax = UserTax::query()->create([
                    'user_id' => $dto->userId,
                    'tax_group_id' => $dto->taxGroupId,
                    'status' => UserTax::ACTIVE,
                    'from' => (clone $givenMonth)->format('Y-m-d'),
                    'payload' => json_encode([
                        'editor_id' => $user->id,
                        'editor_name' => $user->name . " " . $user->last_name,
                        'date' => now()->toDateString(),
                        'comment' => $dto->reason,
                        'action' => 'edit'
                    ])
                ]);
            }
        }
        else {
            $newTax = UserTax::query()->create([
                'user_id' => $dto->userId,
                'tax_group_id' => $dto->taxGroupId,
                'status' => UserTax::REMOVED,
                'from' => (clone $givenMonth)->format('Y-m-d'),
                'to' => (clone $givenMonth)->endOfMonth()->format('Y-m-d'),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->toDateString(),
                    'comment' => $dto->reason,
                    'action' => 'edit'
                ])
            ]);
        }

        $newTax->load('taxGroup.items');
        return $newTax;
    }

    public function deleteUserTax(EditUserTaxDTO $dto): bool
    {
        $givenMonth = Carbon::createFromDate($dto->year, $dto->month);

        /** @var User $user */
        $user = auth()->user();

        /** @var UserTax $userTax */
        $userTax = $this->getUserTaxForGivenMonth($dto->userId, $givenMonth);

        if (!$userTax) return true;

        if ($userTax->status == UserTax::ACTIVE) {
            // Date's month is equal to current month
            if ((clone $givenMonth)->startOfMonth()->eq(now()->startOfMonth())) {
                $this->detachActive((new SetUserTaxDTO(
                    taxGroupId: $userTax->tax_group_id, userId: $userTax->user_id, assigned: false
                )));
            }
            else {
                $userTax->update([
                    'status' => UserTax::REMOVED,
                    'to' => (clone $givenMonth)->endOfMonth()->subDay()->toDateString(),
                    'payload' => json_encode([
                        'editor_id' => $user->id,
                        'editor_name' => $user->name . " " . $user->last_name,
                        'date' => now()->toDateString(),
                        'comment' => $dto->reason,
                        'action' => 'delete'
                    ])
                ]);

                UserTax::query()->create([
                    'user_id' => $userTax->user_id,
                    'tax_group_id' => $userTax->tax_group_id,
                    'status' => UserTax::REMOVED,
                    'from' => (clone $givenMonth)->addMonth()->startOfMonth()->toDateString()
                ]);
            }
        }
        else {
            if (Carbon::parse($userTax->to)->isAfter((clone $givenMonth)->endOfMonth())) {
                UserTax::query()->create([
                    'user_id' => $userTax->user_id,
                    'tax_group_id' => $userTax->tax_group_id,
                    'status' => UserTax::REMOVED,
                    'from' => (clone $givenMonth)->addMonth()->startOfMonth()->toDateString(),
                    'to' => $userTax->to,
                    'payload' => $userTax->payload
                ]);
            }

            $userTax->update([
                'status' => UserTax::REMOVED,
                'to' => (clone $givenMonth)->endOfMonth()->subDay()->toDateString(),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->toDateString(),
                    'comment' => $dto->reason,
                    'action' => 'delete'
                ])
            ]);
        }
        $tax = $this->getUserTaxForGivenMonth($dto->userId, $givenMonth);
        if ($tax) $this->deleteUserTax($dto);
        return true;
    }

    protected function getUserTaxForGivenMonth($userId, $date)
    {
        return UserTax::query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($date) {
                $q->where(function ($q) use ($date) {
                    $q->where('status', UserTax::ACTIVE)
                        ->whereDate('from', '<=', $date->endOfMonth()->format('Y-m-d'))
                        ->whereNull('to');
                })->orWhere(function ($q) use ($date) {
                    $q->where('status', UserTax::REMOVED)
                        ->whereDate('from', '<=', $date->endOfMonth()->format('Y-m-d'))
                        ->whereDate('to', '>=', $date->endOfMonth()->format('Y-m-d'));
                });
            })
            ->latest()
            ->first();
    }

    protected function checkAndCreateForPreviousMonth($date, $userTax, $user): void
    {
        if (!$date->isSameMonth(Carbon::parse($userTax->from))) {
            UserTax::query()->create([
                'user_id' => $userTax->user_id,
                'tax_group_id' => $userTax->tax_group_id,
                'status' => UserTax::REMOVED,
                'from' => $userTax->from,
                'to' => $date->subMonth()->endOfMonth()->toDateString(),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->format('Y-m-d'),
                    'comment' => '',
                    'action' => 'add'
                ])
            ]);
        }
    }

    public function getUserTaxes($userId)
    {
        return UserTax::with('taxGroup:id,name')
            ->where('user_id', $userId)
            ->get();
    }
}
