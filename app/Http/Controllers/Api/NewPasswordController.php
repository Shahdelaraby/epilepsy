<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use App\Models\User;


class NewPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = rand(1000, 9999);

        DB::table('password_resets_otp')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
                'created_at' => now()
            ]
        );


        Mail::raw("كود التفعيل الخاص بك هو: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('رمز التحقق لإعادة تعيين كلمة المرور');
        });

        return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني']);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
            'password' => 'required|confirmed|min:6',
        ]);


        $otpRecord = DB::table('password_resets_otp')
                        ->where('otp_code', $request->code)
                        ->where('otp_expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'الكود خطأ أو انتهت صلاحيته.'], 400);
        }


        $user = User::where('email', $otpRecord->email)->first();

        if (!$user) {
            return response()->json(['message' => 'المستخدم غير موجود.'], 404);
        }


        $user->password = Hash::make($request->password);
        $user->save();

        
        DB::table('password_resets_otp')->where('email', $otpRecord->email)->delete();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح.']);
    }
}

