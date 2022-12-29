<?php

namespace App\Http\Controllers;

class TestController extends Controller
{ 
	public function test() { 

		$token = 'ef5694ded56055c14fa81a3b72f5a38e6dc4d42ac41ea85935a5d0ae6491e2e3';

		$api = new \App\Api\OneCloudApi($token);

		$disk = \Storage::build([
			'driver' => 's3',
			'key' => 'O4493_tenRuslan',
			'secret' => 'ock9m7L55JqA',
			'region' => 'us-east-1', 
			'bucket' => 'tenantbp',
			'endpoint' => 'https://storage.oblako.kz:443',
			'use_path_style_endpoint' => true,
			'throw' => false,
			'visibility' => 'public',
		]);

		dd($disk->files('/books'));
		// dd ($api->changePassword(4832));
		//dd ($api->unblockStorageUser(1) );
		dd ($api->getStorageUsers() );
		dd ($api->createStorageUser('tenRuslan'));
	}

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/token
		$auth_code = 'LALTA3O65P5FCQ8HG1H1VTUCPGO4LFLTO1KLR9S59NVDS0F4A6IFRTA0F5GAFFP7';
		dd((new \App\External\HeadHunter\HeadHunter)->refresh($auth_code));
	}
}
