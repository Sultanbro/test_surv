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
use App\Models\Kpi\Bonus;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\QualityRecordWeeklyStat;

class TestController extends Controller { 
 
	public function test() {
		$qr = QualityRecordWeeklyStat::where('year', 2022)
			->where('month', 8)
			->get();


		foreach ($qr as $key => $r) {
			// save user_stats
			UserStat::saveQuality([
				'date'     => Carbon::createFromDate($r->year, $r->month, $r->day)->format('Y-m-d'),
				'user_id'  => $r->user_id,
				'value'    => $r->total,
				'group_id' => $r->group_id,
			]);
		}

		dd('saved 2');
	}  

	public function test_mail_invitation()
	{
		\Mail::to('abik50000@gmail.com')->send(new \App\Mail\SendInvitation([
			'name' => 'asdasd',
			'email' => 'asdasd',
			'password' => 'asdasd',
			'subdomain' => 'asdasd',
		]));

		return true;
	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'U21A3DAHQTBPGFFPF05KQAM2VPS1D66ON99FIELH69F2GTUEU7DA70I5P4G54JII';
		dd($hh->refreshAccessToken());

	}


}