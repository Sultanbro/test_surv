<?php

namespace App\Service\Tenancy;

use App\Models\CentralUser;
use App\User;

final class CabinetService
{
    public function add(string $tenantId, User $user, bool $is_owner): void
    {
        $centralUser = $this->getCentralUser($user);
        $centralUser->tenants()->syncWithoutDetaching([
            $tenantId => [
                'owner' => $is_owner
            ]
        ]);
    }

    public function remove(string $tenantId, User $user): void
    {
        $centralUser = $this->getCentralUser($user);
        $centralUser->tenants()->detach($tenantId);
    }

    private function getCentralUser(User $user): CentralUser
    {
        /** @var CentralUser */
        return CentralUser::query()->firstOrCreate(
            [
                'email' => $user->email
            ],
            [
                'phone' => $user->phone ?? '77000000000',
                'name' => $user->name ?? 'Noname',
                'last_name' => $user->last_name ?? 'Nolastname',
                'password' => $user->password,
                'birthday' => $user->birthday,
                'city' => $user->working_city,
                'country' => $user->working_country,
                'currency' => $user->currency,
            ]);
    }

    public function getOwnerByTenantId(string $tenantId): CentralUser
    {
        /** @var CentralUser */
        return CentralUser::query()->where('id', $tenantId)->firstOrFail();
    }
}