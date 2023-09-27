<?php

use App\Http\Controllers\Auth\AllAuthController;
use App\Http\Controllers\common\ProductCategroyController;
use App\Http\Controllers\common\ProductController;
use Illuminate\Support\Facades\Route;
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

// Auth Start
Route::group(['prefix' => "auth", 'middleware' => 'api'], function () {
    Route::post('login', [AllAuthController::class, 'login'])->name('login');
    Route::post('register', [AllAuthController::class, 'register']);
    Route::post('register-with', [AllAuthController::class, 'register_with']);
    Route::post('login-with', [AllAuthController::class, 'login_with']);
    Route::post('update-client', [AllAuthController::class, 'update_client']);
    Route::post('update-lawyer', [AllAuthController::class, 'update_lawyer']);
    Route::post('verify-email', [AllAuthController::class, 'verify_email']);
    Route::post('resend-verify-email', [AllAuthController::class, 'resend_verify_email']);
    Route::post('reset-password', [AllAuthController::class, 'reset_password']);
    Route::post('forgot-password', [AllAuthController::class, 'forgot_password']);
    Route::get('refresh', [AllAuthController::class, 'refresh']);
    Route::get('logout', [AllAuthController::class, 'logout']);
    Route::get('user-profile', [AllAuthController::class, 'user_detail']);
});
// Auth End

// SuperAdmin Start
Route::group(["prefix" => "admin", "middleware" => ["auth:api", "isAdmin"]], function () {

    //Product Routes Start
    Route::group(["prefix" => "product"], function () {
        Route::post('/store', [ProductController::class, 'store']);
        Route::get('/list', [ProductController::class, 'list']);
    });
    //Product Routes End


    //Product Category Routes Start
    Route::group(["prefix" => "product-category"], function () {
        Route::post('/store', [ProductCategroyController::class, 'store']);
        Route::get('/list', [ProductCategroyController::class, 'list']);
        Route::post('/list/{id}', [ProductCategroyController::class, 'update']);
        Route::get('/destroy/{id}', [ProductCategroyController::class, 'destroy']);
    });
    //Product Category Routes End


});
// SuperAdmin End
