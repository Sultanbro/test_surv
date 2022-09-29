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
use App\DayType;
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
use App\Http\Controllers\IntellectController;
use App\Models\Bitrix\Lead;
use App\Models\GroupUser;
use App\Salary;

class TestController extends Controller { 
  
	public function test() { 

		$date = '2022-09-23';
		$lead = Lead::with('daytypes')
			->whereHas('daytypes', function ($query) use ($date) {
				$query->whereDate('date', '>', $date)
					->whereIn('type', [
						DayType::DAY_TYPES['TRAINEE'],
						DayType::DAY_TYPES['RETURNED']
					]);
			})
			->whereDate('invite_at', $date)
			->where('resp_id', $managerId)
			->get()
			->count();
		dd($lead);

		$test = '{"document_id":["crm","CCrmDocumentLead","LEAD_549148"],"auth":{"domain":"infinitys.bitrix24.kz","client_endpoint":"https:\/\/infinitys.bitrix24.kz\/rest\/","server_endpoint":"https:\/\/oauth.bitrix.info\/rest\/","member_id":"f1b4c78d4509008a30bd8a97f967759f"},"lead_id":"549148","phone":"87711486535","namex":"\u0422\u0435\u0441\u0442 \u0420\u0443\u0441\u043b\u0430\u043d","email":null,"segment":"\u041a\u0430\u043d\u0434\u0438\u0434\u0430\u0442\u044b (hh, nur \u0438 \u0434\u0440.)","resp_email":"ekudaibergen7@gmail.com"}';
		$test = json_decode($test);

		//dd($test['segment']);
		dd(Lead::getSegment($test->segment));

		dd(tenant());
       // $users    = json_decode(ProfileGroup::query()->findOrFail(53)->users, true);
		dd(static::class);
		// foreach ($users as $key => $user_id) {
		// 	GroupUser::create([
		// 		'user_id'  => $user_id,
		// 		'group_id' => 53,
		// 	]);
		// }
		
		
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
		$hh->auth_code = 'KQO11PIM867V8R3MDUF95O1RGRKQJKKUE8LIK4L96OAM28QTFUO39APUGCUPRVNI';
		dd($hh->refreshAccessToken());

	}


}