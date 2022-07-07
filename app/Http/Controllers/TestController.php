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
use App\Models\TestQuestion;
use App\Models\Books\Book;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestController extends Controller { 
 
	public function test() {
	
		// $user = \App\Kpi::userKpi(10230, '2022-06-01', 1);
		


		// $tests = TestQuestion::where([
		// 	'testable_type' => 'App\\Models\\Books\\Book',
		// 	'testable_id' => 73 
		// ])->get();

		// foreach ($tests as $key => $test) {

		// 	$items = [12, 115,123];
			
		// 	foreach ($items as $key => $item) {
		// 		TestQuestion::create([
		// 			'testable_id' => $item,
		// 			'testable_type' => $test->testable_type,
		// 			'type' => $test->type,
		// 			'text' => $test->text,
		// 			'variants' => $test->variants,
		// 			'page' => $test->page,
		// 			'order' => $test->order,
		// 			'points' => $test->points,
		// 		]);
		// 	}
			

		
		dump(73);
		dd($items);

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
		dd($hh->refreshAccessToken());

	}


}