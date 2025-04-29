<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/verify-reset/{user}', [ForgotPasswordController::class, 'verifyLink'])
    ->name('password.reset')
    ->middleware('signed');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

// Route::get('/login', function () {
//     return view('auth.login');
// });

// Route::get('/forgotpass', function () {
//     return view('auth.forgotpass');
// });

// Route::get('/verifypass', function () {
//     return view('auth.verifypass');
// });

// Route::get('/aboutus', function () {
//     return view('main.aboutus');
// });
