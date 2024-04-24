<?php

namespace App\Repositories\Admin;

use App\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\CoreRepository;
use Illuminate\Support\Facades\DB;
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
            $owner->balance = ($owner->balance ?? 0) . ' ' . strtoupper($owner->currency);
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

        $search = $request->get('query');
        if ($search) {
            $owners->where(function ($q) use ($search){
                $q->where('id', $search)
                    ->orWhere('email', 'like', '%' . trim($search) . '%')
                    ->orWhere('phone', 'like', '%' . trim($search) . '%')
                    ->orWhere('country', 'like', '%' . trim($search) . '%')
                    ->orWhere('lead', 'like', '%' . trim($search) . '%')
                    ->orWhere(DB::raw("CONCAT(users.last_name,' ',users.name)"), 'like', '%' . trim($search) . '%')
                    ->orWhere(DB::raw("CONCAT(users.name,' ',users.last_name)"), 'like', '%' . trim($search) . '%');
            });
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
        $users = User::query()
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

        $defaultManagerId = Setting::query()->where('name', Setting::DEFAULT_MANAGER)->first()?->value;

        foreach ($users as $user) {
            $user->is_default = $user->id == $defaultManagerId ? 1 : 0;
        }

        return $users;
    }
}
