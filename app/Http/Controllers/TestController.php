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

		$musers = \DB::connection('marketing')->table('b_user')->get();

		foreach ($musers as $key => $mar_user) {
			$user = User::withTrashed()->where('id', $mar_user->ID)->where('email', $mar_user->EMAIL)->first();
		
			if($user) {

				dump([
					'user_id' => $user->id,
					'email' => $user->email,
					'name' => $user->last_name . ' ' . $user->name, 
					'created_at' => $mar_user->DATE_REGISTER,
					'deleted_at' => $mar_user->deactivate_date //== '0000-00-00 00:00:00' ? null :  $mar_user->deactivate_date
				]);

			}

		}




		dd($a);
  

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
		dd($hh->refreshAccessToken());

	}


}