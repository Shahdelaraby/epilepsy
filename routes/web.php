<?php

use App\Mail\Epilepsy;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});