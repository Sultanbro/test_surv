<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Exceptions\Registration\TenantUserRegisterException;
use App\Models\CentralUser;
use App\Models\Portal\Portal;
use App\Models\Tenant;
use App\User;
use Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

trait CreateTenant
{
    protected string $password = '';

    public function getNotHashedPassword(): string
    {
        return $this->password;
    }

    public function createTenant(CentralUser $centralUser): Tenant
    {

        $domain = $this->generateRandomDomain();

        /** @var Tenant $tenant */
        $tenant = Tenant::query()
            ->create(['id' => $domain]);

        $tenant->createDomain($domain);

        $centralUser->tenants()->attach($tenant);

        Portal::query()
            ->create([
                'tenant_id' => $tenant->id,
                'owner_id' => $centralUser->getKey(),
                'currency' => $centralUser->currency ?? 'kzt'
            ]);

        return $tenant;
    }


    /**
     * @throws Throwable
     * @throws TenantUserRegisterException
     */
    protected function createTenantUser(Tenant $tenant, array $data): User
    {
        try {
            DB::beginTransaction();
            tenancy()->initialize($tenant);
            /** @var User $user */
            $user = User::query()->create([
                'name' => $data['name'],
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'currency' => $data['currency'],
                'password' => Hash::make($this->password),
                'position_id' => 1,
                'program_id' => 1,
                'is_admin' => 1
            ]);
            $user->description()->create([
                'is_trainee' => 0,
            ]);
            DB::commit();
            return $user;
        } catch (TenantCouldNotBeIdentifiedById|Throwable $e) {
            DB::rollBack();
            throw new TenantUserRegisterException(
                "The tenant user could not be created: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    protected function createCentralUser(array $data): CentralUser
    {
        $this->password = Str::random(8);
        /** @var CentralUser */
        return CentralUser::query()->create([
            'name' => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'phone' => $data['phone'],
            'currency' => $data['currency'],
            'password' => Hash::make($this->password),
        ]);
    }

    protected function generateRandomDomain(): string
    {
        $domain = Str::random(10);

        $domain = Str::lower($domain);

        $exists = Tenant::query()
            ->where('id', $domain)
            ->exists();

        if (!$exists) return $domain; // this is what actually we need

        return $this->generateRandomDomain();
    }

}
