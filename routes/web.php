<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PrivacyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is central app routes of Jobtron.org
| Subdomain routes is located in tenant.php, like bp.jobtron.org
|
 */


// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('_login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('_logout');

// // Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// // Password Reset Routes...
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
//     Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//     Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
//     Route::post('password/reset', 'Auth\ResetPasswordController@reset');

/**
 * Central App routes
 */
Route::get('/', [HomeController::class, 'index']);
Route::get('/test', [HomeController::class, 'test']);

Route::get('/enter/{id}', [TenantController::class, 'enter']);

// tenant routes
Route::get('/projects', [TenantController::class, 'index']);
Route::get('/projects/create', [TenantController::class, 'create']);
Route::get('/projects/edit/{id}', [TenantController::class, 'edit']);
Route::post('/projects/save', [TenantController::class, 'save']);
Route::post('/projects/update', [TenantController::class, 'update']);

// документы компании
Route::get('/aggreement', [PrivacyController::class, 'aggreement']);
Route::get('/offer', [PrivacyController::class, 'offer']);
Route::get('/terms', [PrivacyController::class, 'terms']);

