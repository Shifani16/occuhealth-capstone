<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return redirect('/login');
});

// Important route with signed middleware
Route::get('/verify-reset/{user}', [ForgotPasswordController::class, 'verifyLink'])
    ->name('password.reset')
    ->middleware('signed');

// Catch-all, but EXCLUDING verify-reset
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!verify-reset).*$'); 
