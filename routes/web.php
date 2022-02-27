<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClickLinkController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserLinkController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/test/get', [ClickLinkController::class, 'test']);

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/', [LoginController::class, 'login']);
Route::put('/', [LoginController::class, 'logout']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/{user}/{url}', [ClickLinkController::class, 'visit']);

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    
    Route::get('/link', [UserLinkController::class, 'get']);
    Route::post('/link', [UserLinkController::class, 'store']);
    Route::put('/link/{userLink}', [UserLinkController::class, 'update']);
    Route::delete('/link/{userLink}', [UserLinkController::class, 'destroy']);
    Route::post('/search/link', [UserLinkController::class, 'search']);
    Route::put('/filter/link', [UserLinkController::class, 'filter']);
    
    Route::get('/setting', [SettingController::class, 'index']);
    Route::post('/setting', [SettingController::class, 'submit']);
});

