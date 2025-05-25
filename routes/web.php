<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;

Route::get('/', function () {
    return redirect('/aboutus');
});

Route::get('/verify-reset/{user}', [ForgotPasswordController::class, 'verifyLink'])
    ->name('password.reset')
    ->middleware('signed');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!verify-reset).*$'); 
