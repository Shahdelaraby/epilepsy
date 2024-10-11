<?php

use App\Http\Controllers\ProfileController;
use App\Mail\AccountVeificationMail;
use App\Mail\Epilepsy;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Route::get('/send',function(){
 //   Mail::to('shahdelaraby36@gmail.com')->send(new Epilepsy());
  //  return response('sending');
//});



Route::get('/send',function(){
    Mail::to('shahdelaraby36@gmail.com')->send(new AccountVeificationMail());
    return response('sending');
});



require __DIR__.'/auth.php';

Auth::routes(['verify =>true']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home') ->middleware('verified');
Route::get('/',function(){
   return view('home');
});
