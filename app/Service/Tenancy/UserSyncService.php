<?php

namespace App\Service\Tenancy;

use App\Models\UserCoordinate;
use App\User;

final class UserSyncService
{
    private $syncFields = [
        'password',
        'name',
        'last_name',
        'birthday',
        'working_city',
        'working_country',
    ];

    public function update(string $email, array $data)
    {

        if ($data['coordinates']) {
            $data['coordinate_id'] = $this->setCoordinate($data['coordinates']);
        } else {
            $data['coordinate_id'] = null;
        }

        dd($data);
        $user = User::withTrashed()->where('email', $email)->first();
        $user?->update($data);


        return $user;
    }

    public function setCoordinate(array $coordinatesArray)
    {
        $coordinate = UserCoordinate::query()
            ->where('geo_lat', $coordinatesArray['geo_lat'])
            ->where('geo_lon', $coordinatesArray['geo_lon'])
            ->first();

        if ($coordinate) {
            return $coordinate->id;
        } else {
            $coordinate = UserCoordinate::query()->create([
                'geo_lat' => $coordinatesArray['geo_lat'],
                'geo_lon' => $coordinatesArray['geo_lon']
            ]);
            return $coordinate->id;
        }
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
