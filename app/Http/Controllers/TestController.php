<?php

namespace App\Http\Controllers;

class TestController extends Controller
{ 
	public function test() { 
		$user = \App\Models\GroupUser::where('user_id', 14772)
		->where([
			['status', 'active'],
			//'is_head', false]
		])
		->whereNull('to')
		->get()
		->pluck('group_id')
		->toArray();

        dd($user);
	}

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'LALTA3O65P5FCQ8HG1H1VTUCPGO4LFLTO1KLR9S59NVDS0F4A6IFRTA0F5GAFFP7';
		dd((new \App\External\HeadHunter\HeadHunter)->refresh($auth_code));
	}
}
