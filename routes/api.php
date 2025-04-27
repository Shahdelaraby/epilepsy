<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\VerificationController;
use App\Models\User;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AgoraController;



//Register
Route::post('/register',[ApiController::class,'register']);

//Login
Route::post('/login',[ApiController::class,'login']);

//logout
Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/logout', [ApiController::class, 'logout']);
});


Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'reset']);
Route::post('/verify-otp', [VerificationController::class, 'verifyOtp']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::get('/meetings/{id}', [MeetingController::class, 'show']);
    Route::post('/meetings/{id}', [MeetingController::class, 'join']);
    Route::post('/meetings/{id}/start', [MeetingController::class, 'start']);
    Route::post('/meetings/{id}/end', [MeetingController::class, 'end']);
    Route::post('/meetings/{id}/cancel', [MeetingController::class, 'cancel']);


    Route::post('/agora/token', [AgoraController::class, 'generateToken']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);




    });




