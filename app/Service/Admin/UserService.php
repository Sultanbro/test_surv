<?php

namespace App\Service\Admin;

use App\Classes\Helpers\Currency;
use App\Downloads;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Analytics\TraineeReport;
use App\Models\Analytics\UserStat;
use App\Models\GroupUser;
use App\Photo;
use App\Position;
use App\QualityRecordWeeklyStat;
use App\Zarplata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * Авторизованный пользователь.
     * @var $authUser
     */
    public $authUser;

    public function __construct()
    {
        $this->authUser = \auth()->user();
    }

    /**
     * @return array
     */
    public function getPersonalData(): array
    {   
        /**
         * Валютная ставка
         */
        $currency_rate = in_array($this->authUser->currency, array_keys(Currency::rates()))
            ? (float)Currency::rates()[$this->authUser->currency]
            : 0.0000001;

        /**
         * Должность
         */
        $user_position = Position::find($this->authUser->position_id);

        /**
         * Группы пользователя
         */
        $groups = '';
        $gs = $this->authUser->inGroups();

        foreach($gs as $group) {
            $groups .= '<div>' . $group['name'] . '</div>';
        }

        /**
         * Оклад
         */
        $zarplata = Zarplata::where('user_id', $this->authUser->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;
        $oklad = round($oklad * $currency_rate, 0);
        $oklad = number_format($oklad, 0, '.', ' ');

        /**
         * workday and time
         */
        
        $workingDay = '5-2';
        $workingTime = '09:00 - 18:00';
        
        if($this->authUser->workingDay()) $workingDay = $this->authUser->workingDay()->name;
        if($this->authUser->workingTime()) $workingTime = $this->authUser->workingTime()->name;

        /**
         * Work schedule
         */
        $schedule = substr($this->authUser->work_starts_at(), 0 , 5);
        
        if($this->authUser->work_end) {
            $schedule .= ' - ' . substr($this->authUser->work_end, 0 , 5);
        } else {
            $schedule .= ' - 00:00';
        }

        return [
            'user'        => $this->authUser,
            'position'    => $user_position,
            'groups'      => $groups,
            'salary'      => $oklad,
            'workingDay'  => $workingDay,
            'workingTime' => $workingTime,
            'schedule'    => $schedule,
        ];
    }

    /**
     * Обновление почты.
     * @param UserProfileUpdateRequest $request
     * @return void
     */
    public function updateEmail(UserProfileUpdateRequest $request): void
    {
        $new_email = trim(strtolower($request->email));

        if($this->authUser->email != $new_email) {  // Введен новый email
            $this->authUser->email = $new_email;
            $this->authUser->save();
        }
    }

    /**
     * Обновление валюты.
     * @param $request
     * @return void
     */
    public function updateCurrency($request): void
    {
        if($request->currency != $this->authUser->currency
            && in_array(strtoupper($request->currency), User::CURRENCY)){
            $this->authUser->currency = strtolower($request->currency);
            $this->authUser->save();
        }
    }

    /**
     * Меняем пароль.
     * @param $request
     * @return RedirectResponse|void
     */
    public function changePassword($request)
    {
        if(!empty($request->password)) { // Введен новый пароль
            $this->authUser->password = \Hash::make($request->password);
            $this->authUser->save();

            unset(auth()->user()['can']);
            unset(auth()->user()['groups']);
            Auth::logout();

            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getActivitiesToProfile(Request $request): array
    {
        $activities = '[]';
        $quality    = [];
        $gs         = $this->authUser->inGroups();

        if(count($gs) > 0) {
            $request->group_id = $gs[0]->id;
            $_activities = Activity::where('group_id', $gs[0]->id)->first();

            $activities = UserStat::activities($gs[0]->id , date('Y-m-d'));
            $activities = json_encode($activities);

            $users_ids = (new \App\Service\Department\UserService)->getEmployees($gs[0]->id, date('Y-m-d'));

            $quality = $_activities ? QualityRecordWeeklyStat::table($users_ids, date('Y-m-d')) : [];

        }

        return [
            'activities' => $activities,
            'quality' => $quality
        ];
    }

    /**
     * Отчет стажера.
     * @return array
     */
    public function getTraineeReport(): array
    {
        $trainee_report = [];

        /**
         * костыль Корп универ должен видеть эту таблицу
         * TraineeReport::getBlocks
         */

        $corpUni = tenant('id') == 'bp'
            ? GroupUser::where('user_id', $this->authUser->id)
                ->where('status', 'active')
                ->where('group_id', 96)
                ->first()
            : null;

        /**
         * fetch TraineeReport::getBlocks
         * оценки руководителей
         */
        if($corpUni) {
            $head_in_groups = [1];
            $trainee_report = TraineeReport::getBlocks(date('Y-m-d'));

        }

        return [
            'trainee_report' => $trainee_report
        ];
    }
}