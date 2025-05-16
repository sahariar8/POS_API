<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function () {
    Route::post('/user-registration', [UserController::class, 'UserRegistration']);
    Route::post('/user-login', [UserController::class, 'UserLogin']);
    Route::post('/send-otp', [UserController::class,'SendOTPMail']);
    Route::post('/verify-otp', [UserController::class,'VerifyOTPMail']);
    Route::post('/reset-password', [UserController::class,'ResetPassword'])->middleware([TokenVerificationMiddleware::class]);
    // Route::post('/update-profile', [UserController::class,'UpdateProfile'])->middleware('TokenVerificationMiddleware');
    // Route::post('/change-password', [UserController::class,'ChangePassword'])->middleware('TokenVerificationMiddleware');
});
