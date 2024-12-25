<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;


Route::get('/', function () {
    return 'OK';
});

Route::group(['prefix' => 'appointments'], function () {
    Route::post('/confirm-appointment', [AppointmentController::class, 'confirmAppointment'])->middleware('auth:sanctum');
    Route::get('/view-appointments', [AppointmentController::class, 'viewTodayAppointments'])->middleware('auth:sanctum');
});

Route::apiResource('appointments', AppointmentController::class)->middleware('auth:sanctum');
Route::post('/create-doctor', [AuthController::class, 'createDoctor'])->middleware('auth:sanctum');

Route::group(['prefix' => 'payments'], function () {
    Route::get('/link/{id}', [PaymentController::class, 'getPaymentlink'])->middleware('auth:sanctum');
    Route::get('/callback-success', [PaymentController::class, 'callbackMPSuccess']);
    Route::get('/callback-failure', [PaymentController::class, 'callbackMPFailure']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
;