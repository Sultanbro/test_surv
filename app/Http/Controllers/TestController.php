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
        $auth_code = 'JH1OG3FTG39IU46MANMM3U6UV4U72IBJVMLCQFT9BM35U1DJ8Q6UI85KC95CUNKG';
        dd((new \App\Api\HeadHunterApi2())->refresh($auth_code));
    }
}
