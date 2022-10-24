<?php

namespace App\Http\Controllers;

use App\Service\NotificationService;
use Exception;
use Illuminate\Http\Request;

class NotificationControlller extends Controller
{
    /**
     * @var AwardService
     */
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * @throws Exception
     */
    public function get(Request $request)
    {
        return response()->json([
            'read'   => $this->notificationService->getReadNotifications($request),
            'unread' => $this->notificationService->getUnreadNotifications($request),
            'unreadQuantity' => $this->notificationService->countUnreadNotifications(),
        ]);
    }

}
