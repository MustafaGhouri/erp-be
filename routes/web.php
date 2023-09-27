<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/optimize', function () {
    // Clear the route cache
    Artisan::call('route:clear');

    // Cache the routes
    Artisan::call('route:cache');

    // Clear the application cache
    Artisan::call('cache:clear');

    return "Routes and application cache optimized.";
});
Route::get('/migrate', function () {
    Artisan::call('migrate');
    dd('migrated!');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
