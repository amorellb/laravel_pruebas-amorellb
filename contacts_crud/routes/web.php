<?php

use App\Http\Controllers\ContactsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::resource('contacts', ContactsController::class)
    ->middleware('auth')
    ->parameters(['contacts' => 'contact']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/set_language/{lang}', [App\Http\Controllers\Controller::class, 'set_language'])->name('set_language');

Route::fallback(function () {
    return redirect('/');
});

require __DIR__ . '/auth.php';
