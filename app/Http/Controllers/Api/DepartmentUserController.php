<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\Department\UserService;
use App\User;
use Illuminate\Http\JsonResponse;

class DepartmentUserController extends Controller
{
    /**
     * @var UserService
     */
    public UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @param int $group_id
     * @param String|null $date
     * @return JsonResponse
     */
    public function getUsers(int $group_id = 0 , string $date = null): JsonResponse
    {
        $response = $this->service->getUsers((int) $group_id, (string) $date);

        return response()->json($response);
    }

    /**
     * @param int $group_id
     * @param String|null $date
     * @return JsonResponse
     */
    public function getEmployees(int $group_id = 0 , string $date = null ): JsonResponse
    {
        $response = $this->service->getEmployees((int) $group_id, (string) $date);

        return response()->json($response);
    }

    /**
     * @param int $group_id
     * @param string|null $date
     * @return JsonResponse
     */
    public function getTrainees(int $group_id = 0 , string $date =null): JsonResponse
    {
        $response = $this->service->getTrainees((int) $group_id, (string) $date);

        return response()->json($response);
    }

    /**
     * @param int $group_id
     * @param String|null $date
     * @return JsonResponse
     */
    public function getFiredUsers(int $group_id = 0 , string $date = null ): JsonResponse
    {
        $response = $this->service->getFiredUsers((int) $group_id, (string) $date);

        return response()->json($response);
    }

    /**
     * @param int $group_id
     * @param String|null $date
     * @return JsonResponse
     */
    public function getFiredTrainees(int $group_id = 0 , string $date = null): JsonResponse
    {
        $response = $this->service->getFiredTrainees((int) $group_id, (string) $date);

        return response()->json($response);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function userInGroup(int $id): JsonResponse
    {
        $response = $this->service->userIn($id);

        return response()->json($response);
    }
}
