<?php

namespace App\Http\Controllers\Auth;

use App\Api\BitrixOld\Lead\RegistrationLead;
use App\Http\Controllers\Auth\Traits\CreateTenant;
use App\Http\Controllers\Auth\Traits\LoginToSubDomain;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\RegisterRequest;
use App\Providers\RouteServiceProvider;
use App\User;
use Exception;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    use RedirectsUsers, LoginToSubDomain, CreateTenant;


    protected string $redirectTo = RouteServiceProvider::HOME;

    protected function createCentralUser(array $data): Model|User
    {
        return User::query()->create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'currency' => $data['currency'],
            'password' => Hash::make($data['password']),
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

    /**
     * @throws Exception
     */
    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        if (request()->getHost() !== config('app.domain')) {
            return redirect()->back();
        }

        $data = $request->validated();

        event(new Registered($user = $this->createCentralUser($data)));

        $tenant = $user->tenants()->first() ?? $this->createTenant($user);

        $this->createRegistrationLead($user);

        return response()->json([
            'link' => $this->loginLinkToSubDomain($tenant, $user->email)
        ]);
    }
}
