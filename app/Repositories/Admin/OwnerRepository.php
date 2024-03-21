<?php

namespace App\Repositories\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\CoreRepository;
use Illuminate\Http\Request;
use App\Models\CentralUser;
use Carbon\Carbon;
use App\User;

class OwnerRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return CentralUser::class;
    }

    /**
     * List of owners
     *
     */
    public function getOwnerIds(Request $request): array
    {
        return $this->filter($request)->pluck('id')->toArray();
    }

    /**
     * List of owners
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getOwnersPaginate(Request $request): LengthAwarePaginator
    {
        $owners = $this->filter($request)
            ->with('managerHasOwner.manager')
            ->select([
                'id',
                'last_name',
                'name',
                'email',
                'phone',
                'created_at',
                'login_at',
                'birthday',
                'country',
                'city',
                'lead',
                'balance',
            ])
            ->orderBy($request->get('order_by') ?? 'id', $request->get('order_direction') ?? 'ASC')
            ->paginate($request->get('per_page') ?? 20);

        foreach ($owners as $owner) {
            $subDomains = [];
            foreach ($owner->portals as $portal) {
                $subDomains[] = [
                    'id' => $portal->tenant_id,
                    'currency' => $portal->currency
                ];
            }

            $owner->subdomains = $subDomains;
            $owner->manager = $owner->managerHasOwner?->manager;
            $owner->balance = $owner->balance . ' ' . strtoupper($owner->currency);
            unset($owner->portals);
            unset($owner->managerHasOwner);
        }

        return $owners;
    }

    /**
     * Filter returns query
     *
     * @param Request $request
     * @return Builder
     */
    private function filter(Request $request): Builder
    {
        $owners = $this->model()->query()->withWhereHas('portals');

        if ($request->has('id')) {
            $owners->where('id', $request->get('id'));
        }

        if ($request->has('name')) {
            $owners->where('name', 'like', '%' . trim($request->get('name')) . '%');
        }

        if ($request->has('last_name')) {
            $owners->where('last_name', 'like', '%' . trim($request->get('last_name')) . '%');
        }

        if ($request->has('email')) {
            $owners->where('email', 'like', '%' . trim($request->get('email')) . '%');
        }

        if ($request->has('>login_at')) {
            $owners->whereDate('login_at', '>=', Carbon::parse($request['>login_at']));
        }

        if ($request->has('<login_at')) {
            $owners->whereDate('login_at', '<=', Carbon::parse($request['<login_at']));
        }

        if ($request->has('>birthday')) {
            $owners->whereDate('birthday', '>=', Carbon::parse($request['>birthday']));
        }

        if ($request->has('<birthday')) {
            $owners->whereDate('birthday', '<=', Carbon::parse($request['<birthday']));
        }

        if ($request->has('>balance')) {
            $owners->where('balance', '>=', $request['>balance']);
        }

        if ($request->has('<balance')) {
            $owners->where('balance', '<=', $request['<balance']);
        }

        if ($request->has('country')) {
            $owners->where('country', 'like', '%' . $request->get('country') . '%');
        }

        if ($request->has('city')) {
            $owners->where('city', 'like', '%' . trim($request->get('city')) . '%');
        }

        if ($request->has('lead')) {
            $owners->where('lead', 'like', '%' . trim($request->get('lead')) . '%');
        }

        if ($request->has('subdomains')) {
            if (is_array($request->get('subdomains'))) {
                $owners->whereIn('tenants.id', $request->get('subdomains'));
            } else {
                $owners->where('tenants.id', $request->get('subdomains'));
            }
        }

        return $owners;
    }

    /**
     * List of admins
     * who can log in to admin.jobtron.org
     *
     */
    public function getAdmins()
    {
        return User::query()
            ->select([
                'id',
                'last_name',
                'name',
                'email',
                'is_admin',
                'phone',
                'role_id',
                'img_url',
            ])->get();
    }
}
