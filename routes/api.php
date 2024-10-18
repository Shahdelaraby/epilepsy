<?php

//use App\Http\Controllers\Api;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Models\User;



//Register
Route::post('/register',[ApiController::class,'register']);

//Login
Route::post('/login',[ApiController::class,'login']);


Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/logout', [ApiController::class, 'logout']);
});



Route::post('/email/verification-notification', [EmailVerificationController::class, 'SendVerificationEmail'])->middleware('auth:sanctum');
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify')->middleware('auth:sanctum');


Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'reset']);


/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/
