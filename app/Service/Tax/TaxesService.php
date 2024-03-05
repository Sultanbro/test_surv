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

    public function attach(SetUserTaxDTO $dto, $reason = ''): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->detach($dto);

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

    public function detach(SetUserTaxDTO $dto, $reason = ''): void
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

    public function editUserTax(EditUserTaxDTO $dto): bool
    {
        $date = Carbon::createFromDate($dto->year, $dto->month);
        /** @var User $user */
        $user = auth()->user();

        if ($date->isSameMonth(now())) {
            $this->attach(
                (new SetUserTaxDTO(
                    taxGroupId: $dto->taxGroupId,
                    userId: $dto->userId,
                    assigned: true // it doesn't matter
                )),
                $dto->reason
            );
        } else {
            UserTax::query()->create([
                'user_id' => $dto->userId,
                'tax_group_id' => $dto->taxGroupId,
                'status' => UserTax::ACTIVE,
                'from' => $date->format('Y-m-d'),
                'to' => $date->lastOfMonth()->format('Y-m-d'),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->toDateString(),
                    'comment' => $dto->reason,
                    'action' => 'edit'
                ])
            ]);
        }

        return true;
    }

    public function deleteUserTax(EditUserTaxDTO $dto): bool
    {
        $date = Carbon::createFromDate($dto->year, $dto->month);

        /** @var User $user */
        $user = auth()->user();

        /** @var UserTax $userTax */
        $userTax = UserTax::query()
            ->where('user_id', $user->id)
            ->where('tax_group_id', $dto->taxGroupId)
            ->where(function ($q) use ($date) {
                $q->where('status', UserTax::ACTIVE)
                    ->whereDate('from', '<=', $date->endOfMonth()->format('Y-m-d'))
                    ->whereNull('to');
            })->orWhere(function ($q) use ($date) {
                $q->where('status', UserTax::REMOVED)
                    ->whereDate('from', '<=', $date->endOfMonth()->format('Y-m-d'))
                    ->whereDate('to', '>=', $date->endOfMonth()->format('Y-m-d'));
            })
            ->latest()
            ->first();

        if ($userTax->status == UserTax::ACTIVE) {
            if ($date->isSameMonth(now())) {
                $this->detach( (new SetUserTaxDTO(
                    taxGroupId: $dto->taxGroupId,
                    userId: $dto->userId,
                    assigned: false
                )));
            }
            else {
                // Taxes for previous period(s)
                $this->checkAndCreateForPreviousMonth($date, $userTax, $user);

                // Taxes for next period(s)
                $this->createForNextMonth($date, $userTax, $user);

                $userTax->update([
                    'status' => UserTax::REMOVED,
                    'to' => $date->endOfMonth()->subDay()->toDateString(),
                    'payload' => json_encode([
                        'editor_id' => $user->id,
                        'editor_name' => $user->name . " " . $user->last_name,
                        'date' => now()->toDateString(),
                        'comment' => $dto->reason,
                        'action' => 'delete'
                    ])
                ]);
            }
        }
        else {
            // Taxes for previous period(s)
            $this->checkAndCreateForPreviousMonth($date, $userTax, $user);

            // Taxes for next period(s)
            if (!$date->isSameMonth(Carbon::parse($userTax->to))) {
                $this->createForNextMonth($date, $userTax, $user);
            }

            $userTax->update([
                'to' => $date->endOfMonth()->subDay()->toDateString(),
                'payload' => json_encode([
                    'editor_id' => $user->id,
                    'editor_name' => $user->name . " " . $user->last_name,
                    'date' => now()->toDateString(),
                    'comment' => $dto->reason,
                    'action' => 'delete'
                ])
            ]);
        }

        return true;
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

    protected function createForNextMonth($date, $userTax, $user)
    {
        UserTax::query()->create([
            'user_id' => $userTax->user_id,
            'tax_group_id' => $userTax->tax_group_id,
            'status' => $userTax->status,
            'from' => $date->addMonth()->firstOfMonth()->toDateString(),
            'to' => $userTax->to,
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
