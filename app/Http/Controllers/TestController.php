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
use App\Classes\Analytics\Recruiting;
use App\Classes\Callibro;
use App\DayType;
use App\External\Bitrix\Bitrix;
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
use App\Models\Analytics\AnalyticColumn;
use App\Models\Analytics\RecruiterStat;
use App\Models\Bitrix\Lead;
use App\Models\GroupUser;
use App\Salary;
use App\Service\Department\UserService;

class TestController extends Controller { 
  
	public function test() { 

		$groups = ProfileGroup::where('has_analytics', 1)->get();

		foreach ($groups as $key => $group) {
			

			$columns30 = AnalyticColumn::whereIn('name', ['30'])
				->where('date', '2022-10-01')
				->where('group_id', $group->id)
				->get()
				->pluck('id')
				->toArray();

			$columns31 = AnalyticColumn::whereIn('name', ['31'])
				->where('date', '2022-10-01')
				->where('group_id', $group->id)
				->get()
				->pluck('id')
				->toArray();

			$cols = array_merge($columns30, $columns31);

			$stats = AnalyticStat::whereIn('column_id', $cols)->where('date', '2022-10-01')->where('group_id', $group->id)->get();

			foreach ($stats as $key => $stat) {
				if(in_array($stat->column_id, $columns31) && count($columns30) > 0) {
					
					$el = $stats->where('column_id', $columns30[0])->where('row_id', $stat->row_id)->first();
					if($el) {
						dump($el->toArray());
						$stat->type = $el->type;
						$stat->activity_id = $el->activity_id;
						$stat->value = $el->value;
						$stat->class = $el->class;
						$stat->decimals = $el->decimals;
						$stat->save();

					}
				}
			}

		}
		


	}  

	private function getSegmentAndSaveForLead($id) {

		$res =	(new Bitrix)->getLeads(0, '', 'ALL', 'ASC', '2010-01-01', '2050-01-01', "DATE_CREATE", $id, 'title');
		
		$segment = 999;

		if(array_key_exists('result',$res) && array_key_exists('UF_CRM_1498210379', $res['result'])) {
			$segment = $res['result']['UF_CRM_1498210379'];

			$segment = Lead::getSegmentAlt($segment);
		}

		return $segment;

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
		$hh->auth_code = 'TCH5D6U72JSK1NS6UJNSKR55ESU2IVKQ5C7NSVFGMRE3A13DBUQ6QGA8IR3ORBDM';
		dd($hh->refreshAccessToken());

	}


}