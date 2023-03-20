<?php

namespace App\Repositories\Admin;

use App\Repositories\CoreRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $owner->balance = $owner->balance . ' KZT';
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
    public function getOwnersPaginate(int $perPage = 20, Request $request)
    {
        $owners = $this->filter($request)->select([
                'id',
                'last_name',
                'name',
                'email',
                'created_at',
                'login_at',
                'birthday',
                'country',
                'city',
                'lead',
                'balance',
            ])->paginate($perPage);

        foreach ($owners as $owner) {
            $owner->subdomains = $owner->tenants->pluck('id')->toArray();
            $owner->balance = $owner->balance . ' KZT';
            unset($owner->tenants);
        }

        return  $owners;
    }

    /**
     * Filter returns query
     *
     * @param Request $request
     */
    private function filter(Request $request)
    {
        $owners = $this->model()->has('tenants');

        if($request->has('id')) {
            $owners->where('id', $request->id);
        }

        if($request->has('name')) {
            $owners->where('name', 'like', '%' . trim($request->name) . '%');
        }

        if($request->has('last_name')) {
            $owners->where('last_name',  'like', '%' . trim($request->last_name) . '%');
        }

        if($request->has('email')) {
            $owners->where('email', 'like',  '%' . trim($request->email) . '%');
        }

        if($request->has('>login_at')) {
            $owners->whereDate('login_at', '>=',  Carbon::parse($request['>login_at']));
        }

        if($request->has('<login_at')) {
            $owners->whereDate('login_at', '<=',  Carbon::parse($request['<login_at']));
        }

        if($request->has('>birthday')) {
            $owners->whereDate('birthday', '>=',  Carbon::parse($request['>birthday']));
        }

        if($request->has('<birthday')) {
            $owners->whereDate('birthday', '<=',  Carbon::parse($request['<birthday']));
        }

        if($request->has('>balance')) {
            $owners->where('balance', '>=',  $request['>balance']);
        }

        if($request->has('<balance')) {
            $owners->where('balance', '<=', $request['<balance']);
        }

        if($request->has('country')) {
            $owners->where('country', 'like', '%' . $request->country . '%');
        }

        if($request->has('city')) {
            $owners->where('city', 'like', '%' . trim($request->city) . '%');
        }

        if($request->has('lead')) {
            $owners->where('lead', 'like', '%' . trim($request->lead) . '%');
        }

        if($request->has('subdomains')) {
            if(is_array($request->subdomains)) {
                $owners->whereIn('tenants.id', $request->subdomains);
            } else {
                $owners->where('tenants.id', $request->subdomains);
            }
        }

        return $owners;
    }

    /**
     * List of admins
     * who can login to admin.jobtron.org
     *
     * @return \Illuminate\Support\Collection <\App\User>
     */
    public function getAdmins()
    {
        $admins = User::select([
            'id',
            'last_name',
            'name',
            'email',
            'is_admin',
            'phone',
            'role_id',
            'img_url',
        ])->get();

        return $admins;
    }


}