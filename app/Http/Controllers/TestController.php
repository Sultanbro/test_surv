<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Service\Tenancy\CabinetService;
use Illuminate\Support\Facades\Request;

class TestController extends Controller
{

	public function test() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'RF9AP9HMJGNLG3N84027PC4M7CSPMNNP26V8I845MPTSLCNB40KBHS2H4KBNNNB2';
		dd((new \App\Api\HeadHunter)->refresh($auth_code));
	}

    public function test2() {
        $auth_code = 'GOVE2NFS5958TQD1RJ1PV4RLNA2ODRMKG6TNEIKOGO6IJEK0G2LTACG8TVS14ULV';
        dd((new \App\Api\HeadHunterApi2())->refresh($auth_code));
    }
}
