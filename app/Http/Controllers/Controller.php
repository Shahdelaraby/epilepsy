<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Mail\AccountVerificationMail;
use Illuminate\Support\Facades\Mail;

// إرسال البريد
//Mail::to($user->email)->send(new AccountVerificationMail(''));
abstract class Controller extends BaseController
{
    //
}
