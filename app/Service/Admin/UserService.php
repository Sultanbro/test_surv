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
use App\PositionDescription;
use App\QualityRecordWeeklyStat;
use App\Zarplata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct()
    {
        // auth
    }

    /**
     * @return array
     */
    public function getPersonalData(): array
    {   
        $user = auth()->user();

        /**
         * Валютная ставка
         */
        $currency_rate = in_array($user->currency, array_keys(Currency::rates()))
            ? (float)Currency::rates()[$user->currency]
            : 0.0000001;

        /**
         * Должность
         */
        $user_position = Position::find($user->position_id);

        /**
         * Группы пользователя
         */
        $groups = '';
        $gs = $user->inGroups();

        foreach($gs as $group) {
            $groups .= '<div>' . $group['name'] . '</div>';
        }

        /**
         * Оклад
         */
        $zarplata = Zarplata::where('user_id', $user->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;
        $oklad = round($oklad * $currency_rate, 0);
        $oklad = number_format($oklad, 0, '.', ' ');

        /**
         * workday and time
         */
        
        $workingDay = '5-2';
        $workingTime = '09:00 - 18:00';
        
        if($user->workingDay) $workingDay = $user->workingDay->name;
        if($user->workingTime) $workingTime = $user->workingTime->name;

        /**
         * Work schedule
         */
        $schedule = substr($user->work_starts_at(), 0 , 5);
        
        if($user->work_end) {
            $schedule .= ' - ' . substr($user->work_end, 0 , 5);
        } else {
            $schedule .= ' - 00:00';
        }

        return [
            'user'        => $user,
            'position'    => $user_position,
            'groups'      => $groups,
            'salary'      => $oklad,
            'workingDay'  => $workingDay,
            'workingTime' => $workingTime,
            'schedule'    => $schedule,
            'currency'    => strtoupper($user->currency),
            'currencies'  => [
                'KZT' => 'Казахстанский тенге',
                'RUB' => 'Российский рубль',
                'KGS' => 'Киргизский сом',
                'BYR' => 'Беларуский рубль',
                'UAH' => 'Украинская гривна',
                'UZS' => 'Узбекистанский сум',
            ]
        ];
    }

    /**
     * Обновление почты.
     * @param UserProfileUpdateRequest $request
     * @return void
     */
    public function updateEmail(UserProfileUpdateRequest $request): void
    {   
        $user = auth()->user();

        $new_email = trim(strtolower($request->email));

        if($user->email != $new_email) {  // Введен новый email
            $user->email = $new_email;
            $user->save();
        }
    }

    /**
     * Обновление валюты.
     * @param $request
     * @return void
     */
    public function updateCurrency($request): void
    {   
        $user = auth()->user();

        if($request->currency != $user->currency
            && in_array(strtoupper($request->currency), User::CURRENCY)){
            $user->currency = strtolower($request->currency);
            $user->save();
        }
    }

    /**
     * Меняем пароль.
     * @param $request
     * @return RedirectResponse|void
     */
    public function changePassword($request)
    {   
        $user = auth()->user();

        if(!empty($request->password)) { // Введен новый пароль
            $user->password = \Hash::make($request->password);
            $user->save();

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
        $user = auth()->user();
        $activities = '[]';
        $quality    = [];
        $gs         = $user->inGroups();

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
        $user = auth()->user();

        $trainee_report = [];

        /**
         * костыль Корп универ должен видеть эту таблицу
         * TraineeReport::getBlocks
         */

        $corpUni = tenant('id') == 'bp'
            ? GroupUser::where('user_id', $user->id)
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

     /**
     * Условия оплаты из отделов и должности
     * 
     * @param Request $request
     * @return array
     */
    public function paymentTerms(Request $request): array
    {
        $user = auth()->user();

        $groups = $user->inGroupsWithTerms();

        $groupTerms = [];

        foreach ($groups as $gr) {
            if($gr->payment_terms && $gr->payment_terms != '' && $gr->show_payment_terms == 1) {
                $groupTerms[] = [
                    'title' => 'Отдел: ' . $gr->name,
                    'text' => $gr->payment_terms,
                ];
            }
        }

        $position_desc = PositionDescription::where('position_id', $user->position_id)->first();
   
        return [
            'groups' => $groupTerms,
            'position' => $position_desc && $position_desc->show == 1
                ? $position_desc
                : null
        ];
    }
}