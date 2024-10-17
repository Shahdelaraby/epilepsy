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

Route::group([
    "middleware" => ["auth:sanctum"]
],function(){
//Logout
Route::get('/logout',[ApiController::class,'logout']);
});




//Verified-middleware-example

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent.']);
});


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if (!$request->user()) {
        return response()->json(['message' => 'User not found.'], 404);
    }
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully.']);
})->name('verification.verify');


Route::get('/send-email', function () {
    
    Mail::raw('This is a test email', function ($message) {
        $message->to('rania@gmail.com')
                ->subject('Test Email');
    });

    return response()->json(['message' => 'Email sent successfully!']);
});

Route::middleware(['auth:sanctum']);






/*Route::get('/verfied-middleware-example', function () {
   return response()->json([
        'message' => 'the email account is already confirmed now you are able to see this meeasge.',
    ]);
})->middleware(['auth:sanctum', 'verified']);*/

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/
