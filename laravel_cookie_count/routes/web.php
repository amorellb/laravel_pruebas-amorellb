<?php

use App\Http\Controllers\CookieController;
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

Route::get('/', function () {
    echo 'Visit /cookie-count and reload the page any times';
//    return view('welcome');
});

Route::get('/cookie-count', function () {
    return view('times_visited');
});

Route::get('/cookie-count/{name}', [CookieController::class, "cookie_counter"]);
