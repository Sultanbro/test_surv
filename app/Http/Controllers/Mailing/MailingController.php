<?php

namespace App\Http\Controllers\Mailing;

use App\Facade\MailingFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mailing\CreateMailingRequest;
use App\Http\Requests\Mailing\UpdateMailingRequest;
use App\Models\Mailing\MailingNotification;
use App\Service\Mailing\CreateMailingService;
use App\Service\Mailing\UpdateMailingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MailingController extends Controller
{
    /**
     * @param CreateMailingRequest $request
     * @param CreateMailingService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function create(CreateMailingRequest $request, CreateMailingService $service): JsonResponse
    {
        return $this->response(
            message: 'Success created',
            data: $service->handle($request->toDto())
        );
    }

    /**
     * @param UpdateMailingRequest $request
     * @param UpdateMailingService $service
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateMailingRequest $request, UpdateMailingService $service): JsonResponse
    {
        return $this->response(
            message: 'Success',
            data: $service->handle($request->toDto())
        );
    }
    
    /**
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        $notifications = MailingFacade::fetchNotifications();
        return $this->response(
            message: 'Success',
            data: $notifications
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function find(int $id): JsonResponse
    {
        $userId = \Auth::id() ?? 5;

        if (!MailingFacade::isOwner($id, $userId))
        {
            return $this->response(message: "You don't have permission", data: 403);
        }

        return $this->response(
            message: 'Success',
            data: MailingNotification::query()->findOrFail($id)
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $ownerId = \Auth::id() ?? 5;

        if (!MailingFacade::isOwner($id, $ownerId))
        {
            return $this->response(message: "You don't have permission", data: 403);
        }

        return $this->response(
            message: 'Success deleted',
            data: MailingFacade::deleteNotification($id)
        );
    }
}
