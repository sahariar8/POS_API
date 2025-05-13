<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function () {
    Route::post('/user-registration', [UserController::class, 'UserRegistration']);
    // Route::post('/user-login', [\App\Http\Controllers\UserController::class, 'UserLogin']);
    // Route::post('/user-logout', [\App\Http\Controllers\UserController::class, 'UserLogout']);
    // Route::post('/user-verify-otp', [\App\Http\Controllers\UserController::class, 'UserVerifyOtp']);
});
