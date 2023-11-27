<?php

namespace App\Service\Tenancy;

use App\Models\CentralUser;
use App\User;
use Illuminate\Support\Facades\Hash;

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

    public function update(String $email, array $data)
    {
        $currentTenant = tenant('id');

        $owner = CentralUser::with('tenants')->where('email', $email)->first();

        if(!$owner) {
            return false;
        }

        foreach ($owner->tenants as $tenant) {


            tenancy()->initialize($tenant);

            $users = User::withTrashed()->where('email', $email)->first();

            $users?->update($data);
        }
        $user = User::withTrashed()
            ->where('email',$email)
            ->first()
            ->update($data);

        $owner->update( $this->normalizeForOwner($data) );

        return $user;
    }

    public function normalize(array $data) : array
    {
        $result = [];

        foreach ($this->syncFields as $field) {
            if(array_key_exists($field, $data))  $result[$field] = $data[$field];
        }

        return $result;
    }

    public function normalizeForOwner(array $data) : array
    {
        if(array_key_exists('working_country', $data))  $data['country'] = $data['working_country'];
        if(array_key_exists('working_city', $data))  $data['city'] = $data['working_city'];

        unset($data['working_country']);
        unset($data['working_city']);

        return $data;
    }
}
