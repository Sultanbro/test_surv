<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Mail\PortalCreatedMail;
use App\Models\CentralUser;
use App\Models\Portal\Portal;
use App\Models\Tenant;
use App\Service\Tenancy\CabinetService;
use App\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

trait CreateTenant
{
    /**
     * @throws Exception
     */
    public function createTenant(User $user): Tenant
    {
        $tenant = $this->createTenantWithDomain($user);

        $this->createTenantUser($tenant, $user);

        return $tenant;
    }

    /**
     * @throws Exception
     */
    protected function createTenantWithDomain(User $user): Tenant
    {
        /** @var CentralUser $centralUser */
        $centralUser = CentralUser::query()
            ->where('email', $user->email)
            ->firstOrFail();

        $domain = $this->generateRandomName();

        /** @var Tenant $tenant */
        $tenant = Tenant::query()
            ->create(['id' => $domain]);

        $tenant->createDomain($domain);

        $centralUser->tenants()->attach($tenant);

        (new CabinetService)->add($tenant->id, $user, true);

        Portal::query()
            ->create([
                'tenant_id' => $tenant->id,
                'owner_id' => $user->id,
            ]);

        $mail = new PortalCreatedMail([
            'name' => $centralUser->name,
        ]);
        Mail::to($centralUser->email)->send($mail);

        return $tenant;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function createTenantUser(Tenant $tenant, User $user): User
    {
        tenancy()->initialize($tenant);

        /** @var User $user */
        $user = User::query()->create([
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user['email'],
            'phone' => $user['phone'],
            'currency' => $user['currency'],
            'password' => $user['password'],
            'position_id' => 1,
            'program_id' => 1,
            'is_admin' => 1
        ]);
        $user->description()->create([
            'is_trainee' => 0,
        ]);
        return $user;
    }

    protected function generateRandomName(): string
    {
        $domain = Str::random(10);

        $exists = Tenant::query()
            ->where('id', $domain)
            ->exists();

        if (!$exists) return $domain; // this is what actually we need

        return $this->generateRandomName();
    }
}
