<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('clients', ClientsController::class);

    Route::get('/clients/{client}/journals', [JournalsController::class, 'index'])->name('journals.index');
    Route::post('/clients/{client}/journals', [JournalsController::class, 'store'])->name('journals.store');
    Route::delete('/clients/{client}/journals/{journal}', [JournalsController::class, 'destroy'])->name('journals.destroy');
});
