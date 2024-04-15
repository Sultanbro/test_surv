<?php

namespace App\Http\Controllers\Auth;

use App\Api\BitrixOld\Lead\RegistrationLead;
use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RegisterRequest;
use App\Jobs\Registration\ProcessSendPasswordMail;
use App\Models\CentralUser;
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
use Throwable;

class RegisterController extends Controller
{
    use RedirectsUsers, LoginToSubDomain, CreateTenant;

    protected string $redirectTo = RouteServiceProvider::HOME;

    public function __construct(
        private readonly CabinetService $cabinetService
    )
    {
    }

    public function showRegistrationForm(): Factory|View|Application
    {
        return view('auth.register');
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $centralUser = $this->createCentralUser($data);
        $centralUser->update(['login_at' => now()]);

        $tenant = $centralUser->tenants()->first() ?? $this->createTenant($centralUser);

        $user = $this->createTenantUser($tenant, $data);

        $this->cabinetService->add($tenant->id, $user, true);

        ProcessSendPasswordMail::dispatch($user, $this->getNotHashedPassword());

        $this->createRegistrationLead($user, $centralUser);

        return response()->json([
            'message' => "Check the mailbox"
        ]);
//        return response()->json([
//            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
//        ]);
    }

    private function createRegistrationLead(User $user, CentralUser $centralUser): void
    {
        try {
            $response = (new RegistrationLead($user, null))
                ->setNeedCallback(false)
                ->publish();

            if (array_key_exists('result', $response)) {
                $centralUser->update([
                    'lead' => "https://infinitys.bitrix24.kz/crm/lead/details/" . $response['result']
                ]);
            }
        } catch (Exception) {
            return;
        }
    }
}
