<?php

namespace App\Http\Controllers\Services;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public function index()
    {
        View::share('menu', 'maps');
        View::share('link', 'maps');
        // $allUsers = User::on()->select('working_city')->distinct('working_city')->get()->toArray();

        $maps_array = [];

        // if (!empty($allUsers)) {
        //     foreach ($allUsers as $key => $allUser) {
        //         if($allUser['working_city'] == null) continue;
        //         $allUsersCount = User::on()->where('working_city',$allUser['working_city'])->count();
        //         $geo_location = DB::table('coordinates')->find($allUser['working_city']);
        //         $maps_array[$key]['count'] = $allUsersCount;
        //         $maps_array[$key]['geo_lat'] = $geo_location->geo_lat;
        //         $maps_array[$key]['geo_lon'] = $geo_location->geo_lon;
        //     }
        // }

        return view('surv.maps', compact('maps_array'));
    }

    public function selectedCountryAjaxSearch(Request $request)
    {
        $valueCity = $request['value'];

        $geo_location[] = DB::table('coordinates')
            ->where('city','like', "%$valueCity%")
            ->get()->toArray();

        return $geo_location;
    }
}
