<?php
declare(strict_types=1);

namespace App\Service\Admin\Owners;

use App\Enums\ErrorCode;
use App\Models\Admin\ManagerHasOwner;
use App\Models\CentralUser;
use App\Models\Tariff\TariffSubscription;
use App\Models\Tenant;
use App\Repositories\Tariffs\TariffPaymentRepository;
use App\Support\Core\CustomException;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Класс для работы с Service.
 */
class OwnerInfoService
{
    /**
     * @param TariffPaymentRepository $paymentRepository
     */
    public function __construct(
        public TariffPaymentRepository $paymentRepository
    )
    {
    }

    /**
     * @return array
     */
    public function handle(): array
    {
        $owner = $this->getOwner();
        $domain = $owner->tenants()->where('tenant_id', tenant('id'))->exists();

        abort_if(!$domain, ErrorCode::FORBIDDEN, "У вас не доступа для этого суб домена");

        return [
            'owner' => $owner,
            'tariff' => TariffSubscription::getValidTariffPayment(),
            'users_count' => User::query()->count()
        ];
    }

    /**
     * @return Model
     */
    private function getOwner(): Model
    {
        $user = Auth::user();

        return CentralUser::getByEmail($user->email)->first();
    }
}