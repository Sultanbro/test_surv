<?php


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenantController;


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
Auth::routes();
// Route::any('/auth', function () {
//     return redirect('/');
// });

// Route::get('/login', [LoginController::class, 'in dex'])->name('login');
// Route::get('logout', [LoginController::class, 'logout'])->name('logout');



Route::get('/', [HomeController::class, 'index']);
Route::get('/test', [HomeController::class, 'test']);

Route::get('/projects', [TenantController::class, 'index']);
Route::get('/enter/{id}', [TenantController::class, 'enter']);
Route::get('/projects/create', [TenantController::class, 'create']);
Route::get('/projects/edit/{id}', [TenantController::class, 'edit']);
Route::post('/projects/save', [TenantController::class, 'save']);
Route::post('/projects/update', [TenantController::class, 'update']);

Route::post('logout', [LoginController::class, 'logout'])->name('logout');


