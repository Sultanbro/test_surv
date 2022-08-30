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

class TestController extends Controller { 
 
	public function test() {
		
		$activities = [160, 149];
		
		$date = Carbon::createFromDate(2022, 8, 1);

		$sum_and_counts = \DB::table('user_stats')
			->selectRaw("user_id,
				SUM(value) as sum,
				AVG(value) as avg,
				COUNT(value) as count,
				activity_id,
				date")
			->whereMonth('date', $date->month)
			->whereYear('date', $date->year)
			->groupBy('user_id', 'activity_id');
		
		$users = User::withTrashed()
			->select([
				'users.id',
				'users.last_name',
				'users.name',
				'sum_and_counts.sum',
				'sum_and_counts.avg',
				'sum_and_counts.count', 
				'sum_and_counts.activity_id',
			])
			->leftJoin('user_descriptions as ud', 'ud.user_id', '=', 'users.id')
			->joinSub($sum_and_counts, 'sum_and_counts', function ($join)
			{
				$join->on('users.id', '=', 'sum_and_counts.user_id');
			})
			->where('ud.is_trainee', 0)
		    ->whereIn('sum_and_counts.activity_id', $activities)
			->orderBy('last_name')
			->get();

		$users = $users->groupBy('id')
			->map(function($items) {
				return [
					'id' => $items[0]->id,
					'name' => $items[0]->last_name . ' ' . $items[0]->name,
					'items' => $items->map(function ($item) {
						$item->percent = 0;
						$item->fact = 0;
						$item->share = 0;
						return $item;
					}),
					'expanded' => false
				];
			});

		return $users->values(); //array_values($users->toArray());
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