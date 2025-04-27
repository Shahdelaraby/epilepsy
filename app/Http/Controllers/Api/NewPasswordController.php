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

        // إرسال الإيميل بشكل عادي بدون Mail class
        Mail::raw("كود التفعيل الخاص بك هو: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('رمز التحقق لإعادة تعيين كلمة المرور');
        });

        return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني']);
    }



    public function reset(Request $request)
{
    // التحقق من صحة الإدخالات
    $request->validate([
        'password' => ['required', 'confirmed', RulesPassword::defaults()],
    ]);

    // تحديد المستخدم اللي هيغير الباسورد
    $user = auth()->user(); // لازم تكوني عاملة تسجيل دخول عشان السيرفر يعرف مين المستخدم

    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود أو غير مسجل دخول'], 401);
    }

    // تحديث كلمة المرور
    $user->update([
        'password' => Hash::make($request->password),
        'remember_token' => Str::random(60),
    ]);

    return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
}
}
