<?php

namespace App\Models\Traits;

use App\Models\CentralUser;

trait HasTenants
{
    /**
     * Кабинеты где user owner
     */
    public function tenants(): \Illuminate\Support\Collection
    {
        $centralUser = CentralUser::with('tenants')->where('email', $this->email)->first();

        if(!$centralUser) {
            return collect([]);
        }

        return $centralUser->tenants;
    }

    /**
     * У user есть кабинет где он owner
     */
    public function hasTenant(): bool
    {
        return $this->tenants()->count() > 0;
    }

}