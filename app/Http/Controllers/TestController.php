<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Service\Tenancy\CabinetService;
use Illuminate\Support\Facades\Request;

class TestController extends Controller
{

	public function test() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'RQ5BLDMUIGDTDP2SL2RVLQQ0TPE2N45QQ3BD8AUVFV2UOSBMNJO7C2PPKL10T42I';
		dd((new \App\Api\HeadHunter)->refresh($auth_code));
	}

    public function test2() {
        $auth_code = 'J0KPR11O9UGENJRRO0CBGN451QFMMTQ1QPNH8J8UT1309OH3D2FRQ2T3MTT6L3JR';
        dd((new \App\Api\HeadHunterApi2())->refresh($auth_code));
    }
}
