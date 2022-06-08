<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KnowBase;
use App\Models\TestQuestion;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;


class MapsController extends Controller
{





    public function index(){



        View::share('menu', 'maps');
        View::share('link', 'maps');
        $allUsers = User::on()->select('working_city')->distinct('working_city')->get()->toArray();
        $maps_array = [];
        if (!empty($allUsers)){
            foreach ($allUsers as $key => $allUser){
                $allUsersCount = User::on()->where('working_city',$allUser['working_city'])->count();
                $geo_location = DB::table('coordinates')->find($allUser['working_city']);
                $maps_array[$key]['count'] = $allUsersCount;
                $maps_array[$key]['geo_lat'] = $geo_location->geo_lat;
                $maps_array[$key]['geo_lon'] = $geo_location->geo_lon;
            }
        }









        return view('surv.maps',compact('maps_array'));



    }



    public function selectedCountryAjax(Request$request)
    {


        if ($request['value'] == 2 || $request['value'] == 4 || $request['value'] == 5 || $request['value'] == 6 ){
            return response(['success'=>$request['value']]);
        }else{
            $getCity = DB::table('coordinates')->where('type',$request['value'])->get();
        }


        if (!empty($getCity)){
            return response(['success'=>1,'city'=>$getCity]);
        }else{
            return response(['success'=>0]);
        }

    }


    public function selectedCountryAjaxSearch(Request$request){
        $valueCity = $request['value'];



        $geo_location[] = DB::table('coordinates')->where('type',$request['valueCountry'])
            ->where('city','like', "%$valueCity%")
            ->where('address','like', "%$valueCity%")
            ->get()->toArray();

        return response($geo_location);
    }
}
