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
use App\Http\Controllers\IntellectController;
use App\Salary;

class TestController extends Controller { 
 
	public function test() {

		$date     = Carbon::parse('2022-09-01');
        $group_id = 42;
        $group    = ProfileGroup::query()->findOrFail($group_id);

        $users_ids = [];

        $salaries = [];
        $days = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];

		dd(Salary::getSalaryForDays([
            'date'     => '2022-09-01',
            'group_id' => 48
        ]));

		dd(Salary::salariesTable(
            0, // user_type
            $date->format('Y-m-d'),
            $group->users()->pluck('id')->toArray(), 
            $group_id
        )['users'][9]);
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