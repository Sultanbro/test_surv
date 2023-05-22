<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Service\Tenancy\CabinetService;

class TestController extends Controller
{

	public function test() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'MDGUQDPM2ML50UTBIT6G8DM2L4JQ4L9DHE829U59B1TE0M89JP25GB8Q2G0KUM71';
		dd((new \App\Api\HeadHunter)->refresh($auth_code));
	}
}
