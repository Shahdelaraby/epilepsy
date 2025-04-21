<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $table = 'password_resets_otp';

    protected $fillable = ['email', 'otp_code'];

    public $timestamps = false;
}
