<?php

namespace App\Http\Controllers\Coordinate;

use App\Http\Controllers\Controller;
use App\Models\UserCoordinate;
use App\User;

class getCoordinateController extends Controller
{

    public function get()
    {
        $users = UserCoordinate::query()->with('users')->get();
        $coordinates = $users->map(function ($userCoordinate) {
            $userData = $userCoordinate->users->map(function ($user) {
                return ['id' => $user->id];
            })->toArray();

            return [
                'id' => $userCoordinate->id,
                'users' => $userData,
                'geo_lat' => $userCoordinate->geo_lat,
                'geo_lon' => $userCoordinate->geo_lon,
            ];
        });
        return response()->json([
            'message' => 'success',
            'data'    => $coordinates
        ]);
    }

}