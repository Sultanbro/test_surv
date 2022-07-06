<?php

namespace App\Http\Controllers;

use Auth;
use App\ProfileGroup;
use App\AnalyticsSettingsIndividually;
use App\User;
use App\UserDescription;
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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TestController extends Controller { 
 
	public function test() {
	
		$disk = \Storage::build([
			'driver' => 's3',
			'key' => 'O4493_admin',
			'secret' => 'nzxk4iNukQWx',
			'region' => 'us-east-1',
			'bucket' => 'tenantbp',
			'endpoint' => 'https://storage.oblako.kz:443',
			'use_path_style_endpoint' => true,
			'throw' => false,
			'visibility' => 'public'
		]);
		$url = $disk->temporaryUrl(
			'/videos/pravilnyi_podbor_personala_a512f3a80ea23cbf272f5a8557220a87.mp4', now()->addMinutes(5)
		);
		dd($url);

		//dd($disk->response('videos/videoplayback_f5bd9da50549a093e0f04a769dd7f59e.mp4'));
	}  

	public function hhRefresher() {
		// https://hh.ru/oauth/authorize?response_type=code&client_id=LPAJVTT5AU6U3CJBC1M8RL0KQ5CR2N5OBBEBCHKDK5EJ8V450919BEOMSQOTHNTI&state=um_state&redirect_uri=https://bpartners.kz/
		$hh = new HeadHunter();
		$hh->auth_code = 'PFJT7Q17953OMBNR48N60J2F2M023LNBG8V3K7GSAJB6TF40QDGU82GKQ5LU07ND';
		dd($hh->refreshAccessToken());

	}


}