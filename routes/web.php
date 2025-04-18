<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/forgotpass', function () {
    return view('auth.forgotpass');
});

Route::get('/verifypass', function () {
    return view('auth.verifypass');
});