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
        // التحقق من أن الـ OTP Code موجود وصالح
        $request->validate([
            'otp_code' => 'required|digits:4', // التحقق من أن الكود مكون من 4 أرقام
        ]);

        // البحث عن الكود في جدول password_resets_otp
        $otpRecord = DB::table('password_resets_otp')
                        ->where('otp_code', $request->otp_code)
                        ->where('otp_expires_at', '>', now())  // التحقق من أن الكود لم ينتهِ صلاحيته
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'الكود خطأ أو انتهت صلاحيته'], 400);
        }

        // جلب المستخدم بناءً على الإيميل الموجود في سجل OTP
        $user = User::where('email', $otpRecord->email)->first();

        if ($user) {
            // حفظ الـ user_id في السيشن لتستخدمه في الخطوات التالية
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
    // تحقق من الإيميل اللي هيجيلك
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $email = $request->email;

    // أولاً نحذف الكودات القديمة لهذا الإيميل (لو حابب تبدأ نظيف)
    DB::table('password_resets_otp')
        ->where('email', $email)
        ->delete();

    // ثانياً: نولّد كود جديد
    $otp_code = random_int(1000, 9999); // كود مكون من 4 أرقام

    // نحسب مدة صلاحية الكود (مثلاً 10 دقائق من الآن)
    $otp_expires_at = now()->addMinutes(10);

    // ثالثاً: نحفظ الكود الجديد في جدول password_resets_otp
    DB::table('password_resets_otp')->insert([
        'email' => $email,
        'otp_code' => $otp_code,
        'otp_expires_at' => $otp_expires_at,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // رابعاً: ترسل الكود على الإيميل (بسيطة مبدئياً Mail::raw)
    \Mail::raw("كود التحقق الجديد هو: $otp_code", function ($message) use ($email) {
        $message->to($email)
                ->subject('كود التحقق الجديد');
    });

    // خامساً: ترجع ريسبونس للموبايل أو للفرونت
    return response()->json([
        'message' => 'تم إرسال كود تحقق جديد إلى بريدك الإلكتروني.'
    ]);
}
}
