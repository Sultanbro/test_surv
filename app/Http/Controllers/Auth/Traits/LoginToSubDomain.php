<?php

namespace App\Http\Controllers\Auth\Traits;

use App\Models\CentralUser;
use App\Models\Tenant;
use App\User;
use Exception;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

trait LoginToSubDomain
{
    /**
     * @throws Exception
     */
    public function loginToSubDomain(Tenant $tenant = null, string $email = null)
    {
        if ($tenant) return $this->loginLinkToSubDomain($tenant, $email);

        $links = $this->loginLinks($email);

        if ($links == 0) throw new Exception('User has not tenants to login');

        return $links[0]['link'];
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Exception
     */
    public function loginLinks(string $email = null): array
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        $email = $email ?? $authUser->email;

        $centralUser = $this->getCentralUser($email);

        $links = [];
        foreach ($centralUser->cabinets as $cabinet) {

            $links[] = [
                'id' => $cabinet->id,
                'link' => $this->getSubDomainLink($cabinet, $email)
            ];
        }

        return $links;
    }

    /**
     * @throws Exception
     */
    public function loginLinkToSubDomain(Tenant $tenant = null, string $email = null): string
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        $email = $email ?? $authUser->email;

        $centralUser = $this->getCentralUser($email);

        $tenant = $tenant ?? $centralUser->tenants->first();

        return $this->getSubDomainLink($tenant, $email);
    }

    /**
     * @throws Exception
     */
    protected function getCentralUser(string $email): CentralUser
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        /** @var CentralUser $centralUser */
        $centralUser = CentralUser::with(['tenants', 'cabinets'])
            ->where('email', $email)
            ->first();

        if (!$centralUser) {
            throw new Exception('Can\'t login ' . $authUser->email . '. Owner account was not found in central app (Jobtron DB)');
        }

        return $centralUser;
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     * @throws Exception
     */
    protected function getSubDomainLink(Tenant $tenant, string $email): string
    {
        // target
        $subdomain = $tenant->id . "." . config('app.domain');

        // initialize tenant
        tenancy()->initialize($tenant);

        // find user in tenant app
        $tenantUser = User::query()
            ->where('email', $email)
            ->first();

        if (!$tenantUser) {

            $centralUser = $this->getCentralUser($email);

            $tenantUser = User::query()
                ->create([
                    'email' => $centralUser->email,
                    'phone' => $centralUser->phone,
                    'name' => $centralUser->name,
                    'last_name' => $centralUser->last_name,
                    'working_country' => $centralUser->country,
                    'working_city' => $centralUser->city,
                    'birthday' => $centralUser->birthday,
                    'is_admin' => 0
                ]);
        }

        // redirect link to subdomain  
        $token = tenancy()->impersonate($tenant, $tenantUser->getKey(), '/profile');

        return "https://$subdomain/impersonate/$token->token";
    }
}
