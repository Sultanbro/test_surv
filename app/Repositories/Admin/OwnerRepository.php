<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;

class OwnerRepository extends CoreRepository 
{
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return \App\Models\CentralUser::class;
    }

    /**
     * List of owners
     * 
     * @return \Illuminate\Support\Collection 
     */
    public function getOwners()
    {
        $owners = $this->model()->with('tenants')->select([
            'id',
            'last_name',
            'name',
            'email',
        ])->get();

        foreach ($owners as $owner) {
            $owner->subdomains = $owner->tenants->pluck('id')->toArray();
            $owner->login_at = \Carbon\Carbon::now();
            $owner->birthday = \Carbon\Carbon::parse('1991-01-12');
            $owner->balance = '0 KZT';
            $owner->country = 'Kazakhstan';
            $owner->city = 'Shymkent';
            $owner->lead = 'https://infinitys.bitrix24.kz/crm/lead/details/562872';

            unset($owner->tenants);
        }
        
        return  $owners;
    }   

     /**
     * List of owners
     * 
     * @param int $perPage 
     * @return \Illuminate\Support\Collection 
     */
    public function getOwnersPaginate(int $perPage = 20)
    {
        $owners = $this->model()->with('tenants')->select([
            'id',
            'last_name',
            'name',
            'email',
        ])->paginate($perPage);

        foreach ($owners as $owner) {
            $owner->subdomains = $owner->tenants->pluck('id')->toArray();
            $owner->login_at = \Carbon\Carbon::now();
            $owner->birthday = \Carbon\Carbon::parse('1991-01-12');
            $owner->balance = '0 KZT';
            $owner->country = 'Kazakhstan';
            $owner->city = 'Shymkent';
            $owner->lead = 'https://infinitys.bitrix24.kz/crm/lead/details/562872';

            unset($owner->tenants);
        }
        
        return  $owners;
    }

   
}