<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Mail\PortalCreatedMail;
use App\Models\CentralUser;
use App\Models\Portal\Portal;
use App\Models\Tenant;
use App\User;
use Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

trait CreateTenant
{
    public function createTenant(CentralUser $centralUser): Tenant
    {
        return $this->createTenantWithDomain($centralUser);
    }

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
                'owner_id' => $centralUser->getKey(),
            ]);

        $mail = new PortalCreatedMail([
            'name' => $centralUser->name,
        ]);

        if (!app()->environment('testing')) {
            Mail::to($centralUser->email)->send($mail);
        }

        return $tenant;
    }

    protected function createTenantUser(Tenant $tenant, array $data): User
    {
        try {
            DB::beginTransaction();
            tenancy()->initialize($tenant);
            /** @var User $user */
            $user = User::query()->create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'currency' => $data['currency'],
                'password' => Hash::make($data['password']),
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
            die($e->getMessage());
        }
    }

    protected function createCentralUser(array $data): CentralUser
    {
        /** @var CentralUser */
        return CentralUser::query()->create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'currency' => $data['currency'],
            'password' => Hash::make($data['password']),
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

}
