<?php

namespace App\Http\Controllers\Auth;

use App\Api\BitrixOld\Lead\RegistrationLead;
use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RegisterRequest;
use App\Providers\RouteServiceProvider;
use App\Service\Tenancy\CabinetService;
use App\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    use RedirectsUsers, LoginToSubDomain, CreateTenant;

    protected string $redirectTo = RouteServiceProvider::HOME;

    public function __construct(
        private readonly CabinetService $cabinetService
    )
    {
    }

    public function showForm(): Factory|View|Application
    {
        return view('auth.register');
    }

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $centralUser = $this->createCentralUser($data);

        $tenant = $centralUser->tenants()->first() ?? $this->createTenant($centralUser);

        $user = $this->createTenantUser($tenant, $data);

        $this->cabinetService->add($tenant->id, $user, true);

        $this->createRegistrationLead($user);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }

    private function createRegistrationLead(User $user): void
    {
        try {
            (new RegistrationLead($user, null))
                ->setNeedCallback(false)
                ->publish();
        } catch (Exception) {
            return;
        }
    }
}
