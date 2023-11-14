<?php

namespace App\Service\Tenancy;

use App\Models\CentralUser;
use App\Models\Tenancy\TenantPivot;
use App\User;

final class CabinetService
{
    public function add(string $tenantId, User $user, bool $is_owner): void
    {
        $centralUser = $this->getCentralUser($user);

        $data = [
            'user_id' => $centralUser->id,
            'tenant_id' => $tenantId,
            'owner' => $is_owner ? 1 : 0,
        ];

        $tp = TenantPivot::query()
            ->where($data)->first();

        if (!$tp) TenantPivot::query()
            ->create($data);
    }

    public function remove(string $tenantId, User $user): void
    {
        $centralUser = $this->getCentralUser($user);

        TenantPivot::where([
            'user_id' => $centralUser->email,
            'tenant_id' => $tenantId,
        ])->delete();
    }

    private function getCentralUser(User $user): CentralUser
    {
        $centralUser = CentralUser::where('email', $user->email)->first();

        return $centralUser ?? CentralUser::create([
            'email' => $user->email,
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
        $tenantPivot = TenantPivot::where([
            'tenant_id' => $tenantId,
            'owner' => 1,
        ])
            ->firstOrFail();

        return CentralUser::where('id', $tenantPivot->user_id)->firstOrFail();
    }
}