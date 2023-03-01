<?php

/*
| Central app routes of Jobtron.org
| Subdomain routes is located in tenant.php, like dev.jobtron.org
 */

// Authentication Routes...
use App\Http\Controllers\Settings\OtherSettingController;

Route::get('login', 'Auth\LoginController@showLoginForm')->name('_login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('_logout');

Route::get('login/{subdomain}', 'User\ProjectController@login');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reqest');
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('/setting/reset', [OtherSettingController::class, 'reset']);

// Central App routes
Route::get('/', [\App\Http\Controllers\PageController::class, 'home']);
Route::get('/contacts', [\App\Http\Controllers\PageController::class, 'home'])->name('contacts');
Route::get('/payments', [\App\Http\Controllers\PageController::class, 'home'])->name('payments');

// Company documents
Route::get('/aggreement', [\App\Http\Controllers\PrivacyController::class, 'aggreement']);
Route::get('/offer', [\App\Http\Controllers\PrivacyController::class, 'offer']);
Route::get('/terms', [\App\Http\Controllers\PrivacyController::class, 'terms']);

