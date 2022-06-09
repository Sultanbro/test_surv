<?php

namespace App\Http\Controllers;

use Auth;
use App\ProfileGroup;
use App\AnalyticsSettingsIndividually;
use App\User;
use App\UserDescription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Classes\Analytics\Impl;
use App\Classes\Analytics\PrCstll;
use App\External\HeadHunter\HeadHunter;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Models\QuartalBonus;
use App\KnowBase;
use App\Models\Books\Book;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestController extends Controller { 
 
	public function test() {


        $users = User::find(1);
        $users['password'] = \Hash::make('12345');
        $users->save();

        dd($users);

        $users = \auth()->user();

        dd($users);

        $users = User::whereNull('name')->whereNull('last_name')->get();

		$uds = UserDescription::whereIn('user_id', $users->pluck('id')->toArray())->get();
		foreach ($uds as $key => $ud) {
			$ud->is_trainee = 1;
			$ud->save();
		}
		//dd($uds);
//         \Hash::make('12345');
//        $user->save();
        //\Auth::login($user);

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
		dd($hh->refreshAccessToken());

	}


}