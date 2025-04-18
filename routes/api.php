<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\dashboard\PatientController;
use App\Http\Controllers\dashboard\McuPatientController;
use App\Http\Controllers\dashboard\McuResultController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [AuthController::class, 'getUsers']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::patch('/users/{id}', [AuthController::class, 'update']);
Route::delete('/users/{id}', [AuthController::class, 'delete']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'getPatients']);
    Route::get('/{id}', [PatientController::class, 'show']);
    Route::post('/', [PatientController::class, 'store']);
    Route::put('/{id}', [PatientController::class, 'update']);
    Route::delete('/{id}', [PatientController::class, 'destroy']);
});

Route::prefix('mcu-patients')->group(function () {
    Route::get('/', [McuPatientController::class, 'index']);
    Route::get('/{id}', [McuPatientController::class, 'show']);
    Route::post('/', [McuPatientController::class, 'store']);
    Route::put('/{id}', [McuPatientController::class, 'update']);
    Route::delete('/{id}', [McuPatientController::class, 'destroy']);
});

Route::prefix('mcu-results')->group(function () {
    Route::get('/', [McuResultController::class, 'index']);
    Route::get('/by-patient/{id}', [McuResultController::class, 'indexByPatient']);
    Route::get('/{id}', [McuResultController::class, 'show']);
    Route::post('/', [McuResultController::class, 'store']);
    Route::put('/{id}', [McuResultController::class, 'update']);
    Route::delete('/{id}', [McuResultController::class, 'destroy']);
});