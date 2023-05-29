<?php

namespace App;

use App\Fine;
use App\UserNotification;
use App\TimetrackingHistory;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserFine extends Model
{
    const SATURDAY = "6";
    const SUNDAY = "0";

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    protected $fillable = [
        'status'
    ];

    /**
     * Добавление штрафа к пользователю
     *
     * @param array $data
     * @return integer
     */
    public function addUserFine(array $data)
    {
        $userFine = new UserFine;
        $userFine->user_id = $data['user_id'];
        $userFine->fine_id = $data['fine_id'];
        $userFine->day = $data['day'];
        $userFine->note = $data['note'];
        $userFine->save();

        $title = 'Добавлен штраф на '. Carbon::parse($data['day'])->format('d.m.Y');
        self::setNotificationAboutFine($data['user_id'], $data['fine_id'], $title);

        return $userFine->id;
    }

    public function fine()
    {
        return $this->hasOne('App\Fine', 'id', 'fine_id');
    }


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
     * @param int $user_id
     * @param string $date
     * @return mixed
     */
    public function getAmountUserFines(int $user_id, string $date)
    {
        $fines = UserFine::whereDate('day', $date)
            ->where('user_id', $user_id)
            ->count();
        return $fines;
    }

    /**
     * @param int $user_id
     * @param int $fine_id
     * @param string $date
     * @return void
     */
    public static function turnOffFine(int $user_id, int $fine_id, string $date)
    {
        $fine = UserFine::whereDate('day', $date)
            ->where('user_id', '=',  $user_id)
            ->where('fine_id','=',  $fine_id)
            ->where('status','=',  1)
            ->first();

        if (!is_null($fine)) {
            $fine->status = UserFine::STATUS_INACTIVE;
            $fine->save();

            $title = 'Удален штраф на '. Carbon::parse($date)->format('d.m.Y');
            self::setNotificationAboutFine($user_id, $fine_id, $title);
        }
    }

    /**
     * @param int $user_id
     * @param int $fine_id
     * @param string $date
     * @return void
     */
    public static function turnOnFine(int $user_id, int $fine_id, string $date)
    {
        $fine = UserFine::whereDate('day', $date)
            ->where('user_id', '=',  $user_id)
            ->where('fine_id','=',  $fine_id)
            ->where('status','=',  2)
            ->first();

        if (!is_null($fine)) {
            $fine->status = UserFine::STATUS_ACTIVE;
            $fine->save();
            UserFine::updateTimetracking($user_id, $date);

            $title = 'Добавлен штраф на '. Carbon::parse($date)->format('d.m.Y');
            self::setNotificationAboutFine($user_id, $fine_id, $title);
        }
    }

    /**
     * @param int $user_id
     * @param string $date
     * @return void
     */
    public static function updateTimetracking(int $user_id, string $date)
    {
        // сохраняем признак что были выполнены изменения, возможно надо будет код закоментировать
        $date = explode("-", $date);
        $year = $date[0];
        $month = $date[1];
        $day = $date[2];
        // вот здесь надо обновлять ячейку TimeTracking
        $timeTrackingDay = Timetracking::where('user_id', $user_id)
            ->whereYear('enter', intval($year))
            ->whereMonth('enter', intval($month))
            ->whereDay('enter', $day)
            ->selectRaw('*')
            ->orderBy('id', 'ASC')
            ->first();

        if(is_null($timeTrackingDay)) {
            return 'Нельзя редактировать день с пустым значением!';
        }
        $timeTrackingDay->updated = 1;
        $timeTrackingDay->save();
    }

    public static function setNotificationAboutFine($user_id, $fine_id, $title, $data = [])
    {   
        $message = self::getFineDescription($fine_id);
        
        if($fine_id == 53 && array_key_exists("date", $data))  {
            $title = $message;
        	$message = self::getTemplate('2500', $data['date']);
        }

        UserNotification::create([
            'user_id' => $user_id,
            'title' => $title,
            'message' => $message,
        ]);
        
        if(array_key_exists("date", $data)) {
            TimetrackingHistory::create([
                'user_id' => $user_id,
                'author_id' => 5,
                'author' => 'Система',
                'date' => $data['date'],
                'description' => $message,
            ]);
        }
        
    }

    private static function getFineDescription($fine_id)
    {   
        $fine = Fine::find($fine_id);
        if($fine) {
            $description = $fine->name;
        } else {
            $description = 'Штраф id = '.$fine_id.' не найден';
        }
        
        return $description;
    }

    public static function getTemplate($sum, $date)
    {   
        
        $fine_id = 53; // штраф за невыход на работу
        $notification_template = DB::table('notification_templates')
		    ->find(1); // шаблон штрафа 

        if($notification_template) {
            $message = $notification_template->message;
            $message = str_replace("#sum", $sum, $message);  // замена суммы в сообщении
            $message = str_replace("#date", $date, $message); // замена даты в сообщении
            return $message;
        } else {
            return 'Вам назначен штраф за невыход на работу в рабочий день! '. $date;
        }


    }
}
