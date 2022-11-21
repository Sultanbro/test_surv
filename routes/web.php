<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\Admin\GroupAnalyticsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
// Auth::routes();
// Route::any('/auth', function () {
//     return redirect('/');
// });

// Route::get('/login', [LoginController::class, 'index'])->name('login');
// Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('_login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('_logout');

// // Registration Routes...
// $this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// $this->post('register', 'Auth\RegisterController@register');

// // Password Reset Routes...
// $this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
// $this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// $this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
// $this->post('password/reset', 'Auth\ResetPasswordController@reset');

/**
 * Central App routes
 */
Route::get('/', [HomeController::class, 'index']);
Route::get('/test', [HomeController::class, 'test']);

Route::get('/projects', [TenantController::class, 'index']);
Route::get('/enter/{id}', [TenantController::class, 'enter']);
Route::get('/projects/create', [TenantController::class, 'create']);
Route::get('/projects/edit/{id}', [TenantController::class, 'edit']);
Route::post('/projects/save', [TenantController::class, 'save']);
Route::post('/projects/update', [TenantController::class, 'update']);

Route::get('upload_file', function () {
    return view('upload');
});
use App\Http\Controllers\FileUploadController;
 
Route::post('store_file', [FileUploadController::class, 'fileStore']);


//Route::post('logout', [LoginController::class, 'logout'])->name('logout');

