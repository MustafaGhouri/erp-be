<?php

use App\Http\Controllers\Auth\AllAuthController;
use App\Http\Controllers\Common\BrandsController;
use App\Http\Controllers\Common\CustomerController;
use App\Http\Controllers\Common\DepartmentController;
use App\Http\Controllers\Common\LocationsController;
use App\Http\Controllers\Common\ProductCategroyController;
use App\Http\Controllers\Common\ProductController as CommonProductController;
use App\Http\Controllers\Common\ProductModelController;
use App\Http\Controllers\Common\RegionsController;
use App\Http\Controllers\Common\UnitsController;
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

// Admin Start
Route::group(["prefix" => "admin", "middleware" => ["auth:api", "isAdmin"]], function () {

    //Product Routes Start
    Route::group(["prefix" => "products"], function () {
        Route::post('/store', [CommonProductController::class, 'store']);
        Route::get('/list', [CommonProductController::class, 'list']);
    });
    //Product Routes End


    //Product Category Routes Start
    Route::group(["prefix" => "product-category"], function () {
        Route::post('/store', [ProductCategroyController::class, 'store']);
        Route::get('/list', [ProductCategroyController::class, 'list']);
        Route::post('/update/{id}', [ProductCategroyController::class, 'update']);
        Route::get('/destroy/{id}', [ProductCategroyController::class, 'destroy']);
    });
    //Product Category Routes End

    //units Category Routes Start
    Route::group(["prefix" => "units"], function () {
        Route::post('/store', [UnitsController::class, 'store']);
        Route::get('/list', [UnitsController::class, 'list']);
    });
    //units Category Routes End

    //Brands Category Routes Start
    Route::group(["prefix" => "brands"], function () {
        Route::post('/store', [BrandsController::class, 'store']);
        Route::get('/list', [BrandsController::class, 'list']);
    });
    //Brands Category Routes End

    //Printer Routes Start
    Route::group(["prefix" => "product-model"], function () {
        Route::post('/store', [ProductModelController::class, 'store']);
        Route::post('/bulk-store', [ProductModelController::class, 'bulk_store']);
        Route::get('/list', [ProductModelController::class, 'list']);
        Route::get('/list-brand/{id}', [ProductModelController::class, 'list_by_brands']);
    });
    //Printer Routes End

    //Regions Routes Start
    Route::group(["prefix" => "region"], function () {
        Route::get('/list', [RegionsController::class, 'list']);
    });
    //Regions Routes End

    //Location Routes Start
    Route::group(["prefix" => "location"], function () {
        Route::post('/bulk-store', [LocationsController::class, 'bulk_store']);
        Route::post('/store', [LocationsController::class, 'store']);
        Route::get('/region-list/{id}', [LocationsController::class, 'list_by_region']);
        Route::get('/list', [LocationsController::class, 'list']);
        Route::get('/destroy/{id}', [LocationsController::class, 'destroy']);
    });
    //Location Routes End

    //department Routes Start
    Route::group(["prefix" => "department"], function () {
        Route::post('/bulk-store', [DepartmentController::class, 'bulk_store']);
        Route::post('/store', [DepartmentController::class, 'store']);
        Route::get('/list', [DepartmentController::class, 'list']);
        Route::get('/destroy/{id}', [DepartmentController::class, 'destroy']);
    });
    //department Routes End

    //department Routes Start
    Route::group(["prefix" => "customer"], function () {
        Route::post('/bulk-store', [CustomerController::class, 'bulk_store']);
        Route::post('/store', [CustomerController::class, 'store']);
        Route::get('/list', [CustomerController::class, 'list']);
        Route::get('/destroy/{id}', [CustomerController::class, 'destroy']);
    });
    //department Routes End


});
// Admin End
