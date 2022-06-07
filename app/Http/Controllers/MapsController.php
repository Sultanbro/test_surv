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




        $coordinates = DB::table('coordinates')->where('type','!=',2)->get()->toArray();





        return view('surv.maps',compact('coordinates'));



    }



    public function selectedCountryAjax(Request$request)
    {

        if ($request['value'] == 2){
            return response(['success'=>2]);
        }else{
            $getCity = DB::table('coordinates')->where('type',$request['value'])->get();
        }


        if (!empty($getCity)){
            return response(['success'=>1,'city'=>$getCity]);
        }else{
            return response(['success'=>0]);
        }

    }
}
