<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Service\Tenancy\CabinetService;
use Exception;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{

    /**
     * @param CabinetService $cabinetService
     */
    public function __construct(public CabinetService $cabinetService)
    {}

    /**
     * получить овнера компании
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getCompanyOwner(): JsonResponse
    {
        $tenantId = tenant('id');

        if (!isset($tenantId)) {
            throw new Exception('have no tenantId');
        }

        $owner = $this->cabinetService->getOwnerByTenantId($tenantId);

        return $this->response(
            message: 'Success',
            data: $owner
        );
    }
}
