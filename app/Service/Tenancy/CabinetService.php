<?php

namespace App\Service\Tenancy;

use App\Models\CentralUser;
use App\Models\Tenancy\TenantPivot;
use App\User;
use DB;

final class CabinetService
{   
    public function add(String $tenantId, User $user): void
    {       
        $centralUser = $this->getCentralUser($user);

        $data = [
            'user_id' => $centralUser->id,
            'tenant_id' => $tenantId,
        ];

        $tp = TenantPivot::where($data)->first();

        if(!$tp) TenantPivot::create($data);
    }

    public function remove(String $tenantId, User $user): void
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

    
}