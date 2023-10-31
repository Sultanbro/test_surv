<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Service\Tenancy\CabinetService;
use Illuminate\Support\Facades\Request;

class TestController extends Controller
{

	public function test() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'OD1E5P7URCJ7P6K0ARQHC13BD9MQ4QLJAEE2PFVOSM4QENQCRFS2PJSLK2390N4E';
		dd((new \App\Api\HeadHunter)->refresh($auth_code));
	}

    public function test2() {
        $auth_code = 'J8KOT95362INC0LJK1IKSIS470QBM3B5NP078T5C7PNE7BQB0A636GLMJMK50U9J';
        dd((new \App\Api\HeadHunterApi2())->refresh($auth_code));
    }
}
