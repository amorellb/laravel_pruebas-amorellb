<?php

use App\Http\Controllers\CookieController;
use App\Http\Controllers\ScheduleController;
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
    echo 'Visit /cookie-count and reload the page any times<br>';
    echo 'Visit /cookie-count/name and reload the page any times<br>';
    echo 'Visit /es/schedule to add and see your contacts<br>';
//    return view('welcome');
});

// Recuento the veces que un mismo usuario recarga la página.
Route::get('/cookie-count', function () {
    return view('times_visited');
});

// Recuento the veces que un mismo usuario recarga la página. Se pasa el nombre por la URL
Route::get('/cookie-count/{name}', [CookieController::class, "cookie_counter"]);

// Formulario con traducción automática
Route::get('/schedule/{lang}', [ScheduleController::class, "render_form"]);
Route::post('/schedule/contacts', [ScheduleController::class, "render_schedule"])->name('contacts');
