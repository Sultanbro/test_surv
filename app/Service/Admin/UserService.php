<?php

namespace App\Service\Admin;

use App\Http\Requests\UserProfileUpdateRequest;
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

    public function __construct($authUser)
    {
        $this->authUser = $authUser;
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
}