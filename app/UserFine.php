<?php

namespace App;

use App\Repositories\UserFineRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property int fine_id
 * @property int day
 * @property string note
 * @property string status
 */
class UserFine extends Model
{
    const SATURDAY = "6";
    const SUNDAY = "0";

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    protected $fillable = [
        'status'
    ];

    public static function getFinesByUser($request, $id, $name)
    {
        $fines = [];
        $userFines = UserFine::where('user_id', $id)
            ->whereMonth('day', '=', $request->month)
            ->whereYear('day', '=', $request->year)
            ->where('status', '=', self::STATUS_ACTIVE)
            ->get();

        $workingDays = DB::table('timetracking')->orderBy('id')->where('user_id', $id)->whereMonth('enter', '=', $request->month)->whereYear('enter', '=', $request->year)->pluck('enter');
        $onlyDaysWorking = [];
        foreach ($workingDays as $workingDay) {
            $onlyDaysWorking[] = date("Y-m-d", strtotime($workingDay));
        }

        $correctFines = 0;
        foreach ($userFines as $fine) {

            $day = date("Y-m-d", strtotime($fine->day));
            $dayOfWeek = date("w", strtotime($fine->day));

            if ($fine->fine_id == Fine::TYPE_LATE_MORE_5 || $fine->fine_id == Fine::TYPE_LATE_LESS_5) {
                // сначала проверяем выходной ли это день
                if ($dayOfWeek != self::SATURDAY && $dayOfWeek != self::SUNDAY) {
                    // а теперь проверяем, работал ли сотрудник вообще в этот день
                    if (in_array($day, $onlyDaysWorking)) {
                        $fines[] = $fine;
                        if ($fine->fine_id == Fine::TYPE_LATE_MORE_5) {

                        } elseif ($fine->fine_id == Fine::TYPE_LATE_LESS_5) {

                        }

                        $correctFines++;
                    }
                }
            } else {
                $fines[] = $fine;
            }

        }
//        if ($correctFines > 1) {
//           "Работал но опоздал $correctFines для $name"
//        }

        return $fines;
    }

    /**
     * @param int $userId
     * @param int $fineId
     * @param string $date
     * @return void
     */
    public static function turnOffFine(int $userId, int $fineId, string $date)
    {
        $fine = (new UserFineRepository)->getUserFine($userId, $fineId, $date)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        if (!is_null($fine)) {
            $fine->status = UserFine::STATUS_INACTIVE;
            $fine->save();

            $title = 'Удален штраф на ' . Carbon::parse($date)->format('d.m.Y');
            self::setNotificationAboutFine($userId, $fineId, $title);
        }
    }

    /**
     * @param int $userId
     * @param int $fineId
     * @param string $date
     * @return ?UserFine
     */
    public static function turnOnFine(int $userId, int $fineId, string $date): ?UserFine
    {
        /** @var UserFine $fine */
        $fine = (new UserFineRepository)->getUserFine($userId, $fineId, $date)
            ->where('status', self::STATUS_INACTIVE)
            ->first();

        if (!is_null($fine)) {
            $fine->status = UserFine::STATUS_ACTIVE;
            $fine->save();
            UserFine::updateTimetracking($userId, $date);

            $title = 'Добавлен штраф на ' . Carbon::parse($date)->format('d.m.Y');
            self::setNotificationAboutFine($userId, $fineId, $title);

        }
        return $fine;
    }

    /**
     * @param int $userId
     * @param string $date
     * @return void
     */
    public static function updateTimetracking(int $userId, string $date)
    {
        // сохраняем признак что были выполнены изменения, возможно надо будет код закоментировать
        $date = explode("-", $date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        // вот здесь надо обновлять ячейку TimeTracking
        $timeTrackingDay = Timetracking::where('user_id', $userId)
            ->whereYear('enter', intval($year))
            ->whereMonth('enter', intval($month))
            ->whereDay('enter', $day)
            ->selectRaw('*')
            ->orderBy('id', 'ASC')
            ->first();

        if (is_null($timeTrackingDay)) {
            return 'Нельзя редактировать день с пустым значением!';
        }
        $timeTrackingDay->updated = 1;
        $timeTrackingDay->save();
    }

    /**
     * Добавление штрафа к пользователю
     *
     * @param array $data
     * @return UserFine
     */
    public function addUserFine(array $data): UserFine
    {
        $userFine = new UserFine;
        $userFine->user_id = $data['user_id'];
        $userFine->fine_id = $data['fine_id'];
        $userFine->day = $data['day'];
        $userFine->note = $data['note'];
        $userFine->save();

        $title = 'Добавлен штраф на ' . Carbon::parse($data['day'])->format('d.m.Y');
        self::setNotificationAboutFine($data['user_id'], $data['fine_id'], $title);

        return $userFine;
    }

    public static function setNotificationAboutFine($userId, $fineId, $title, $data = [])
    {
        $message = self::getFineDescription($fineId);

        if ($fineId == 53 && array_key_exists("date", $data)) {
            $title = $message;
            $message = self::getTemplate('2500', $data['date']);
        }

        UserNotification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
        ]);

        if (array_key_exists("date", $data)) {
            TimetrackingHistory::create([
                'user_id' => $userId,
                'author_id' => 5,
                'author' => 'Система',
                'date' => $data['date'],
                'description' => $message,
            ]);
        }

    }

    private static function getFineDescription($fineId)
    {
        $fine = Fine::find($fineId);
        if ($fine) {
            $description = $fine->name;
        } else {
            $description = 'Штраф id = ' . $fineId . ' не найден';
        }

        return $description;
    }

    public static function getTemplate($sum, $date)
    {

        $fineId = 53; // штраф за невыход на работу
        $notification_template = DB::table('notification_templates')
            ->find(1); // шаблон штрафа

        if ($notification_template) {
            $message = $notification_template->message;
            $message = str_replace("#sum", $sum, $message);  // замена суммы в сообщении
            $message = str_replace("#date", $date, $message); // замена даты в сообщении
            return $message;
        } else {
            return 'Вам назначен штраф за невыход на работу в рабочий день! ' . $date;
        }


    }

    public function fine()
    {
        return $this->hasOne('App\Fine', 'id', 'fine_id');
    }

    /**
     * @param int $userId
     * @param string $date
     * @return mixed
     */
    public function getAmountUserFines(int $userId, string $date)
    {
        $fines = UserFine::whereDate('day', $date)
            ->where('user_id', $userId)
            ->count();
        return $fines;
    }
}
