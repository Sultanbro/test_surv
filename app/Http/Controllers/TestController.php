<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;

class TestController extends Controller
{ 
	public function test() { 
		$user = CentralUser::with('cabinets')->get()->toArray();
		dd($user);
	}

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'LALTA3O65P5FCQ8HG1H1VTUCPGO4LFLTO1KLR9S59NVDS0F4A6IFRTA0F5GAFFP7';
		dd((new \App\Api\HeadHunter)->refresh($auth_code));
	}
}
