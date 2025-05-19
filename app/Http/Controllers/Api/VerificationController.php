<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller {

    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(["msg" => "Email verified.", [
            "user" => $user,
            'token' => $user->createToken('API TOKEN')->plainTextToken,
        ]], 200);
    }
    public function verifyOtp(Request $request)
    {

        $request->validate([
            'otp_code' => 'required|digits:4',
        ]);


        $otpRecord = DB::table('password_resets_otp')
                        ->where('otp_code', $request->otp_code)
                        ->where('otp_expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'الكود خطأ أو انتهت صلاحيته'], 400);
        }

        $user = User::where('email', $otpRecord->email)->first();

        if ($user) {

            session(['reset_user_id' => $user->id]);

            return response()->json([
                'message' => 'تم التحقق من الكود بنجاح. يمكنك الآن إعادة تعيين كلمة المرور.'
            ]);
        } else {
            return response()->json(['message' => 'المستخدم غير موجود'], 404);
        }
    }

    public function resendOtp(Request $request)
{

    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $email = $request->email;


    DB::table('password_resets_otp')
        ->where('email', $email)
        ->delete();


    $otp_code = random_int(1000, 9999);


    $otp_expires_at = now()->addMinutes(10);


    DB::table('password_resets_otp')->insert([
        'email' => $email,
        'otp_code' => $otp_code,
        'otp_expires_at' => $otp_expires_at,
        'created_at' => now(),
        'updated_at' => now(),
    ]);


    \Mail::raw("كود التحقق الجديد هو: $otp_code", function ($message) use ($email) {
        $message->to($email)
                ->subject('كود التحقق الجديد');
    });

    
    return response()->json([
        'message' => 'تم إرسال كود تحقق جديد إلى بريدك الإلكتروني.'
    ]);
}
}
