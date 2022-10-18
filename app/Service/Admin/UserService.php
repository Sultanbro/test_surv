<?php

namespace App\Service\Admin;

use App\Classes\Helpers\Currency;
use App\Downloads;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Analytics\Activity;
use App\Models\Analytics\UserStat;
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
        $this->authUser = User::findOrFail(5);
    }

    /**
     * @return array
     */
    public function getPersonalData(): array
    {
        $currency_rate = in_array($this->authUser->currency, array_keys(Currency::rates())) ? (float)Currency::rates()[$this->authUser->currency] : 0.0000001;

        $positions = Position::all();
        $photo     = Photo::where('user_id', $this->authUser->id)->first();
        $downloads = Downloads::where('user_id', $this->authUser->id)->first();
        $user_position = Position::find($this->authUser->position_id);

        /*** Группы пользователя */
        $groups = '';
        $gs = $this->authUser->inGroups();

        foreach($gs as $group) {
            $groups .= '<div>' . $group['name'] . '</div>';
        }

        $zarplata = Zarplata::where('user_id', $this->authUser->id)->first();

        $oklad = 0;
        if($zarplata) $oklad = $zarplata->zarplata;
        $oklad = round($oklad * $currency_rate, 0);
        $oklad = number_format($oklad, 0, '.', ' ');

        return [
            'user'           => $this->authUser,
            'positions'      => $positions,
            'photo'          => $photo,
            'downloads'      => $downloads,
            'user_positions' => $user_position,
            'groups'         => $groups,
            'salary'         => $oklad
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
}