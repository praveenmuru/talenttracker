<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpeningController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [LoginController::class, 'showForm'])->name('login.form');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/login', [LoginController::class, 'showForm'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::resource('openings', OpeningController::class);
    Route::post('candidates/parse-resume', [CandidateController::class, 'parseResume'])->name('candidates.parse');
    Route::resource('candidates', CandidateController::class);
});
Route::resource('candidates', CandidateController::class)->except(['parse-resume']);
