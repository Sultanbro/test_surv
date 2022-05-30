<?php

namespace App\Http\Controllers;

use Auth;
use App\ProfileGroup;
use App\AnalyticsSettingsIndividually;
use App\User;
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

class TestController extends Controller {
 
	public function test() {
		$users_ids = 
		$a = User:: //leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
		//->where('ud.is_trainee', 0)
		
			whereIn('id', $users_ids)
			->get()
			->pluck('id')
			->toArray(); 
		
		dd($a);
	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
		dd($hh->refreshAccessToken());

	}


}