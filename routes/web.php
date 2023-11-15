<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Lead\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\Settings\OtherSettingController;
use App\Http\Controllers\User\ProjectController;
use App\Http\Middleware\OnlyCentralDomain;

//--------------------/login routs/--------------------------//
Route::get('login', [LoginController::class, 'showLoginForm'])->name('_login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('_logout');
Route::get('login/{subdomain}', [ProjectController::class, 'login']);
//--------------------/login routs/--------------------------//

/*******************************************************************************************/

//--------------------/registration routs/--------------------------//
Route::middleware(OnlyCentralDomain::class)
    ->group(function () {
        Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register']);
    });
//--------------------/registration routs/--------------------------//

/*******************************************************************************************/

//--------------------/reset routs/--------------------------//
Route::post('/setting/reset', [OtherSettingController::class, 'reset']);
//--------------------/reset routs/--------------------------//

/*******************************************************************************************/

//--------------------/central app routs/--------------------------//
Route::get('/', [PageController::class, 'home']);
Route::get('/contacts', [PageController::class, 'home'])->name('contacts');
Route::get('/payments', [PageController::class, 'home'])->name('payments');
Route::get('/contract-offer', [PageController::class, 'home'])->name('contract-offer');
Route::get('/site-use-agreement', [PageController::class, 'home'])->name('site-use-agreement');
Route::get('/personal-data', [PageController::class, 'home'])->name('personal-data');
Route::get('/privacy-policy', [PageController::class, 'home'])->name('privacy-policy');
//--------------------/central app routs/--------------------------//

/*******************************************************************************************/

//--------------------/company routs/--------------------------//
Route::get('/aggreement', [PrivacyController::class, 'aggreement']);
Route::get('/offer', [PrivacyController::class, 'offer']);
Route::get('/terms', [PrivacyController::class, 'terms']);
Route::post('/create_lead', [LeadController::class, 'createLead']);
//--------------------/company routs/--------------------------//
