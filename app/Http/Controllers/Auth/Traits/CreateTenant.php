<?php

namespace App\Http\Controllers\Auth\Traits;

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
    private ?string $password = null;
    private ?string $hashedPassword = null;


    protected function createTenantWithDomain(CentralUser $centralUser): Tenant
    {
        $domain = $this->generateRandomName();

        /** @var Tenant $tenant */
        $tenant = Tenant::query()
            ->create(['id' => $domain]);

        $tenant->createDomain($domain);

        $centralUser->tenants()->attach($tenant);

        Portal::query()
            ->create([
                'tenant_id' => $tenant->id,
                'owner_id' => $centralUser->id,
                'currency' => $centralUser->currency ?? 'kzt'
            ]);

        return $tenant;
    }

    protected function createTenantUser(Tenant $tenant, array $data): User
    {
        $fullName = explode(' ', $data['name']);

        try {
            tenancy()->initialize($tenant);
            DB::beginTransaction();
            /** @var User $user */
            $user = User::query()->create([
                'name' => $fullName[0],
                'last_name' => $fullName[1] ?? '',
                'email' => $data['email'],
                'phone' => $data['phone'],
                'currency' => $data['currency'],
                'password' => $data['password'] ?? $this->hashedPassword,
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
            tenant()->delete();
            die($e->getMessage());
        }
    }

    protected function createCentralUser(array $data): CentralUser
    {
        $fullName = explode(' ', $data['name']);

        /** @var CentralUser */
        return CentralUser::query()->create([
            'name' => $fullName[0],
            'last_name' => $fullName[1] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'],
            'currency' => $data['currency'],
            'password' => $this->hashedPassword,
        ]);
    }

    protected function generateRandomName(): string
    {
        $domain = Str::random(10);

        $domain = Str::lower($domain);

        $exists = Tenant::query()
            ->where('id', $domain)
            ->exists();

        if (!$exists) return $domain; // this is what actually we need

        return $this->generateRandomName();
    }

    private function generatePassword(): void
    {
        $this->password = Str::random(10);
        $this->hashedPassword = Hash::make($this->password);
    }

    public function getGeneratedPassword(): string
    {
        return $this->password;
    }

}
