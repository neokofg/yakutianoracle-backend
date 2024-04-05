<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// User
use App\Http\Controllers\User\GetController as UserGet;
// Auth
use App\Http\Controllers\Auth\LoginController as AuthLogin;
// Email
use App\Http\Controllers\Email\VerifyController as EmailVerify;
// Geo
use App\Http\Controllers\Geo\StoreController as GeoStore;
use App\Http\Controllers\Geo\GetController as GeoGet;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/', AuthLogin::class);
    });
    Route::prefix('user')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', UserGet::class);
        Route::prefix('/geo')->group(function () {
            Route::get('/', GeoGet::class);
            Route::post('/', GeoStore::class);
        });
    });
    Route::prefix('verify')->group(function () {
        Route::get('/{token}', EmailVerify::class);
    });
});
