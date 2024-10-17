<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\EmailVerificationController;


//Register
Route::post('/register',[ApiController::class,'register']);

//Login
Route::post('/login',[ApiController::class,'login']);


Route::post('/email/verification-notification', function (Request $request) {
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

Route::group([
    "middleware" => ["auth:sanctum", 'verified'],
], function () {

Route::get('/users', [\App\Http\Controllers\Api\UsersController::class, 'index']);


//Logout
Route::get('/logout',[ApiController::class,'logout']);
});





Route::middleware(['auth:sanctum']);




/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/
