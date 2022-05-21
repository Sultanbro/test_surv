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
		$a = \App\User::find(5)->toArray();
		//array_push($a, [1231]);

		
		dd($a);
		// $kb = KnowBase::with('children','questions')->find(3);
		// $arr = [];
		// KnowBase::getArray($arr, $kb);
		//$storagePath  = \Storage::disk('dispos')->get($temp_path);
		//dd($storagePath);
		


		dd(\Storage::exists('public/' . '/Evgenii-jigilin-master-zvonka.pdf')); 

	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
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