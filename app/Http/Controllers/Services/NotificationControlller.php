<?php

namespace App\Http\Controllers\Services;

use App\Classes\Analytics\Recruiting;
use App\Http\Requests\Notification\NotificationReadRequest;
use App\Http\Requests\Notification\UnReadCountRequest;
use App\Models\Admin\History;
use App\Service\NotificationService;
use App\Http\Controllers\Controller;
use App\UserAbsenceCause;
use App\UserNotification;
use App\UserReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationControlller extends Controller
{
    /**
     * @var AwardService
     */
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
//        $this->middleware('auth');
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

    /**
     * Отметить прочитанным
     *
     * Нужно разделить логику:
     * пропал с обучения
     * просто отметка прочитано
     * перенос стажировки
     * сохранение отчета
     */
    public function setRead(Request $request)
    {
        $user_id = auth()->id();

        $noti = UserNotification::where('user_id', $user_id)
            ->where('id', $request->id)
            ->first();

        if($noti) {
            $timestamp = $noti->group;

            $noti->read_at = now();

            /**
             * есть комментарий
             */
            if($request->comment) {
                $noti->note = $request->comment;

                /**
                 * Уведомление относится к какому-то лицу: Лид Стажер или Сотрудник
                 * Название уведомления : Пропал с обучения
                 */
                if($noti->about_id != 0) {

                    $type = UserAbsenceCause::THIRD_DAY;
                    if($noti->title == 'Пропал с обучения: 1 день') $type = UserAbsenceCause::FIRST_DAY;
                    if($noti->title == 'Пропал с обучения: 2 день') $type = UserAbsenceCause::SECOND_DAY;

                    UserAbsenceCause::createOrUpdate([
                        'user_id' => $noti->about_id,
                        'date'    => Carbon::now()->day(1)->format('Y-m-d'),
                        'type'    => $type,
                        'text'    => $request->comment,
                    ]);
                }
            }

            $noti->save();

            /**
             * Отметить прочитанными похожие сообщения
             * но только отправленные другим
             *
             * Пример: Пропал с обучения
             *
             * Рекрутер решает конфликт,
             * после этого у других рекрутеров исчезает смысл в их обработке
             */
            if($noti->about_id != 0) {
                $copies = UserNotification::where('group', $timestamp)->get();

                foreach ($copies as $copy) {
                    $copy->read_at = now();
                    $copy->save();
                }
            }

            /**
             * Transfer training to another date
             * перенос стажировки
             */
            if($request->type && $request->type == 'transfer') {

                $result = Recruiting::transferTraining($request->user_id, $request->date, $request->time);

                $data = [
                    'data' => $request->all(),
                ];

                if($result != 1) {
                   $data['error'] = $result;
                }

                History::user($user_id, 'Перенос обучения', $data);

                return  $result;
            }

            /**
             * Сохранить отчет
             */
            if($request->type && $request->type == 'report') {
                UserReport::create([
                    'user_id' => $user_id,
                    'title'   => 'Отчет недельный',
                    'date'    => date('Y-m-d'),
                    'text'    => $request->text
                ]);
            }

            return 1; // ok
        }

        return 0; // not found
    }

    /**
     * setNotiReadAll
     */
    public function setAllRead(Request $request)
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now()
            ]);

        return 1;
    }

    /**
     * @param NotificationReadRequest $request
     * @return JsonResponse
     */
    public function read(NotificationReadRequest $request): JsonResponse
    {
        $dto = $request->toDto();

        return $this->response(
            message: 'Success',
            data: $this->notificationService->setRead($dto->userNotificationId)
        );
    }

    /**
     * @param UnReadCountRequest $request
     * @return JsonResponse
     */
    public function unReadCount(UnReadCountRequest $request): JsonResponse
    {
        $userId = (int) $request->query('user_id', 0);
        return $this->response(
            message: 'Success',
            data: $this->notificationService->unRead($userId)
        );
    }
}
