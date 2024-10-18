<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;



//Register
Route::post('/register',[ApiController::class,'register']);

//Login
Route::post('/login',[ApiController::class,'login']);

//Verifivation email
/*Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent.']);
});

Route::get('email/verify/{id}', [\App\Http\Controllers\Api\VerificationController::class,'verify'])->name('verification.verify');
Route::get('email/resend', [\App\Http\Controllers\Api\VerificationController::class,'resend'])->name('verification.resend');

Route::get('/send-email', function () {

    Mail::raw('This is a test email', function ($message) {
        $message->to('rania@gmail.com')
                ->subject('Test Email');
    });

    return response()->json(['message' => 'Email sent successfully!']);
});
Route::middleware(['auth:sanctum']);*/



Route::group([
    "middleware" => ["auth:sanctum", 'verified'],
], function () {

//Logout
Route::get('/logout',[ApiController::class,'logout']);
});



Route::get('/protected-route', function () {
    return response()->json(['message' => 'This is a protected route!']);
})->middleware(['verified']);



/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/
