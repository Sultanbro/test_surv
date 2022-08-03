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
use App\Models\Books\BookSegment;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestController extends Controller { 
 
	public function test() {
	
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$g = \App\Kpi::userKpi(15551, '2022-07-01', 1);
		// 15030
		dd($g);
		// $books = Book::get();
		// foreach($books as $book) {
		// 	$tests = TestQuestion::where([
		// 		'testable_type' => 'App\\Models\\Books\\BookSegment',
		// 		'testable_id' => $book->id
		// 	])->get();

		// 	$groups = $tests->groupBy('page');

		// 	foreach ($groups as $page => $els) {
				
		// 		$segment = BookSegment::create([
		// 			'title' => 'test',
		// 			'book_id' => $book->id,
		// 			'page_start' => $page,
		// 			'page_end' => $page,
		// 			'pass_grade' => 100
		// 		]);

		// 		foreach ($els as $key => $q) {
		// 			$q->testable_id = $segment->id;
		// 			$q->save();
		// 		}
		// 	}
		// }

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'U21A3DAHQTBPGFFPF05KQAM2VPS1D66ON99FIELH69F2GTUEU7DA70I5P4G54JII';
		dd($hh->refreshAccessToken());

	}


}