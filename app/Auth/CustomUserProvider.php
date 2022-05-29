<?php

namespace App\Auth;

use App\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class CustomUserProvider extends EloquentUserProvider
{
	public function validateCredentials(UserContract $user, array $credentials) {
		$plain = $credentials['password'];

		if(!$user->active_in_admin) {
			return false;
		}

		$salt = substr($user->PASSWORD, 0, (strlen($user->PASSWORD) - 32));
		$realPassword = substr($user->PASSWORD, -32);
		$password = md5($salt.$plain);

		///dd(User::get());.

		// $user = User::find($user->id);
		// Auth::login($user);
		$result = ($password == $realPassword);
		if($result) {
            User::set_timezone_of($user->id);
		    $user = User::find($user->id);
            $user->AUTH_TIME = date('Y-m-d H:i:s');
            $user->save();
        }
		return $result;
	}
}
