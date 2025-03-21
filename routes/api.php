<?php

//use App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Models\User;
use App\Http\Controllers\MeetingController;

Route::middleware('api')->get('/test',function()
{
    return "test best";
});
Route::get('/test', function () {
    return "test best";
});

//Register
Route::post('/register',[ApiController::class,'register']);

//Login
Route::post('/login',[ApiController::class,'login']);


Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/logout', [ApiController::class, 'logout']);
});



Route::post('/email/verification-notification', [EmailVerificationController::class, 'SendVerificationEmail'])->middleware('auth:sanctum');
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');


Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'reset']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/meetings', [MeetingController::class, 'store']);
    Route::get('/meetings', [MeetingController::class, 'index']);
    Route::get('/meetings/{id}', [MeetingController::class, 'show']);
    Route::post('/meetings/{id}/join', [MeetingController::class, 'join']);
    Route::post('/meetings/{id}/startMeet', [MeetingController::class, 'startMeet']);
    Route::post('/meetings/{id}/end', [MeetingController::class, 'end']);
    Route::post('/meetings/{id}/cancel', [MeetingController::class, 'cancel']);


    });
