<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\CandidateController;

// Route::get('/', function () {
//     return view('welcome');
// });


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/login', [LoginController::class, 'showForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');


Route::resource('openings', OpeningController::class);

// ADD THIS NEW ROUTE
Route::post('candidates/parse-resume', [CandidateController::class, 'parseResume'])
       ->middleware('auth')
       ->name('candidates.parse');
       
Route::resource('candidates', CandidateController::class);
