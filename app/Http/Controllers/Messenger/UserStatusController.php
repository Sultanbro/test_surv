<?php

namespace App\Http\Controllers\Messenger;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserStatusController extends Controller
{
    /**
     * Обновляет посещение пользователя.
     *
     * @return void
     */
    public function online(): void
    {
        $authId = auth()->id();

        if (Auth::check())
        {
            $user = User::getUserById($authId);
            $user->last_seen = now();
            $user->save();
        }
    }
}
