<?php

namespace App\Console\Commands\Employee;

use App\Fine;
use App\User;
use App\UserFine;
use App\Timetracking;
use App\TimetrackingHistory;
use App\ProfileGroup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:late {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Штрафы за опоздание';

    /**
     * 'Y-m-d'
     */
    protected $date;

    /**
     * Сотрудник может начать день до 20 минут от своего рабочего времени.
     */
    protected int $ignoreMinutes = 20;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::now();

        $users = User::query()->withWhereHas('user_description', fn ($query) => $query->where('is_trainee', 0))
            ->orderBy('last_name', 'asc')
            ->get();

        foreach($users as $user)
        {
            $userFine = new UserFine;

            /**
             * Отнимаем 6 часов так как время сервера GTM +0.
             * Время начала смены для сотрудника.
             */
            $workStart = Carbon::createFromTimeString($user->work_starts_at())->subHours(6)->subMinutes(10)->format('Y-m-d H:i:s');

            /**
             * Получаем запись из timetracking таблицы.
             */
            $startDay = Timetracking::query()->where('user_id', $user->id)->whereDate('enter', $this->date);

            /**
             * Был ли штраф на ту дату, которую передаем.
             */
            $fines = UserFine::query()->whereDate('day', $this->date)->where('user_id', $user->id)->get();

            if ($startDay->exists())
            {
                $startDayInTimestamp = strtotime($startDay->min('enter'));
                $workStartTimeStamp  = strtotime($workStart);

                /**
                 * Разница в минутах.
                 */
                $diffInMinutes = round(($startDayInTimestamp - $workStartTimeStamp) / 60);

                /**
                 * Если минута 0 или меньше 0, то сотрудник пришел вовремя.
                 */
                if ($diffInMinutes <= 0)
                {
                    continue;
                }

                /**
                 * Штраф до 5 минут.
                 */
                if ($diffInMinutes <= 5)
                {
                    $existActive = $fines->where('fine_id', Fine::TYPE_LATE_LESS_5)->where('status',UserFine::STATUS_ACTIVE)->count() > 0;
                    $existInActive = $fines->where('fine_id', Fine::TYPE_LATE_LESS_5)->where('status',UserFine::STATUS_INACTIVE)->count() > 0;

                    if (!$existActive && !$existInActive)
                    {
                        /**
                         * Создаем штраф менее 5 минут.
                         */
                        $userFine->addUserFine([
                            'user_id'   => (int) $user->id,
                            'fine_id'   => Fine::TYPE_LATE_LESS_5,
                            'day'       => $this->date,
                            'status'    => UserFine::STATUS_ACTIVE,
                            'note'      => null
                        ]);

                        /**
                         * Создаем новую запись в истории Timetracking.
                         */
                        $this->history($user->id, 'За приход на работу с опозданием до 5 минут');
                    }
                }

                /**
                 * Штраф после 5 минут.
                 */
                if ($diffInMinutes > 5)
                {

                    $existActive = $fines->where('fine_id', Fine::TYPE_LATE_MORE_5)->where('status',UserFine::STATUS_ACTIVE)->count() > 0;
                    $existInActive = $fines->where('fine_id', Fine::TYPE_LATE_MORE_5)->where('status',UserFine::STATUS_INACTIVE)->count() > 0;

                    if (!$existActive && !$existInActive)
                    {

                        /**
                         * Создаем штраф более 5 минут.
                         */
                        $userFine->addUserFine([
                            'user_id'   => (int) $user->id,
                            'fine_id'   => Fine::TYPE_LATE_MORE_5,
                            'day'       => $this->date,
                            'status'    => UserFine::STATUS_ACTIVE,
                            'note'      => null
                        ]);

                        /**
                         * Создаем новую запись в истории Timetracking.
                         */
                        $this->history($user->id, 'За приход на работу с опозданием от 5 минут и более');
                    }
                }
            }

        }
    }

    public function history(int $userId, $message){

        $th = TimetrackingHistory::query()->whereDate('date', $this->date)
            ->where('user_id',  $userId)
            ->where('description', 'like', $message)
            ->first();

        if(!$th) {
            TimetrackingHistory::query()->create([
                'user_id' => $userId,
                'author_id' => 5,
                'author' => 'Система',
                'date' => $this->date,
                'description' => $message,
            ]);
        }
    }
}