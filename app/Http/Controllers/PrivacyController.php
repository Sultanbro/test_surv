<?php

namespace App\Http\Controllers;

class PrivacyController extends Controller { 
	
	public function aggreement()
	{ 
        return view('privacy.aggreement')->with('title', 'Пользовательское соглашение');
	}

	public function terms()
	{ 
        return view('privacy.terms')->with('title', 'Политика конфиденциальности');
	}

	public function offer()
	{ 
        return view('privacy.offer')->with('title', 'Договор оферты');
	}
}
