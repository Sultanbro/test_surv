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
use App\External\HeadHunter;
use App\Models\Analytics\AnalyticStat;
use App\Models\Analytics\UserStat;
use App\Models\QuartalBonus;

class TestController extends Controller {
 
	public function test() {

	    $a = QuartalBonus::create([
            'user_id'=> 1,
            'auth_id'=> 1,
            'quartal'=> 11,
            'sum'=> 0,
            'year'=> date('Y'),
            'text'=> 'sfdsdfsdf',
        ]);

		dd($a); 

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'T1D4JV4BSSPS2T0CO5745QK3FGMOLNLHAQ46BQCPMJL93LI9RSR3QAULFR60667P';
		dd($hh->refreshAccessToken());
		
	}

	public function asiToUserStat() {
	
		$date = '2022-01-01';

		$asis = AnalyticsSettingsIndividually::where('date', $date)
			->where('group_id', '!=', 48)
			->get();

		$saved = 0;
		foreach($asis as $asi) {
			$data = json_decode($asi->data, true);

			for($i = 1;$i<=31;$i++) {
				if(array_key_exists($i, $data)) {
					
					if($asi->employee_id == null) continue;
					$us = UserStat::where([
						'user_id' => $asi->employee_id,
						'date' => Carbon::parse($date)->day($i)->format('Y-m-d'),
						'activity_id' => $asi->type
					])->first();

					if($us) {
						$us->value = $data[$i];
						$us->save();
					} else {
						//dump($data[$i]);
						UserStat::create([
							'user_id' => $asi->employee_id,
							'date' => Carbon::parse($date)->day($i)->format('Y-m-d'),
							'activity_id' => $asi->type,
							'value' => $data[$i],
						]);

					}

					$saved++;
				}
			}
			
		}

		dump($saved); 
		dd($asis->count()); 

	}  
}