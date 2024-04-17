<?php

namespace App\Service\Tenancy;

use App\Models\CentralUser;
use App\Models\UserCoordinate;
use App\Service\Settings\UserUpdateService;
use App\User;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

final class UserSyncService
{
    private array $syncFields = [
        'password',
        'name',
        'last_name',
        'birthday',
        'working_city',
        'working_country',
    ];

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function update(string $keyEmail, array $data): bool
    {
        $currentTenant = tenant('id');
        $generalParams = $this->getGeneralParams($data);

        // 1-step. Update central user
        /** @var CentralUser $centralUser */
        $centralUser = CentralUser::with('cabinets')->where('email', $keyEmail)->first();
        $centralUser->update($generalParams);

        // 2-step. Update current tenant user data with all params
        $this->updateCurrentTenantUserData($keyEmail, $data);

        // 3-step. Update other tenants
        UserUpdateService::updateTenantUserData($centralUser, $currentTenant, $generalParams, $keyEmail);

        // 4-step. Back all connections to current tenant
        tenancy()->initialize($currentTenant);

        return true;
    }

    public function updateCurrentTenantUserData($keyEmail, $data): void
    {
        if ($data['coordinates']) {
            $data['coordinate_id'] = $this->setCoordinate($data['coordinates']);
        } else {
            $data['coordinate_id'] = null;
        }

        $user = User::withTrashed()->where('email', $keyEmail)->first();
        $user?->update($data);
    }

    public function getGeneralParams($data): array
    {
        $params = [];

        $params['password'] = $data['password'];
        $params['email'] = $data['email'];
        $params['name'] = $data['name'];
        $params['last_name'] = $data['last_name'];
        $params['phone'] = $data['phone'];
        $params['birthday'] = $data['birthday'];
        $params['country'] = $data['working_country'];

        return $params;
    }

    public function setCoordinate(array $coordinatesArray)
    {
        /** @var UserCoordinate $coordinate */
        $coordinate = UserCoordinate::query()
            ->where('geo_lat', $coordinatesArray['geo_lat'])
            ->where('geo_lon', $coordinatesArray['geo_lon'])
            ->first();

        if (!$coordinate) {
            $coordinate = UserCoordinate::query()->create([
                'geo_lat' => $coordinatesArray['geo_lat'],
                'geo_lon' => $coordinatesArray['geo_lon']
            ]);
        }
        return $coordinate->id;
    }

    public function normalizeForOwner(array $data): array
    {
        if (array_key_exists('working_country', $data)) $data['country'] = $data['working_country'];
        if (array_key_exists('working_city', $data)) $data['city'] = $data['working_city'];

        unset($data['working_country']);
        unset($data['working_city']);

        return $data;
    }

    public function normalize(array $data): array
    {
        $result = [];

        foreach ($this->syncFields as $field) {
            if (array_key_exists($field, $data)) $result[$field] = $data[$field];
        }

        return $result;
    }
}
