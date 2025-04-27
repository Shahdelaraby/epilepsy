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
            'otp_code' => 'required|digits:4', // التحقق من أن الكود مكون من 4 أرقام (تعديل العدد حسب الحاجة)
        ]);

        // البحث عن الكود في جدول password_resets_otp
        $otpRecord = DB::table('password_resets_otp')
                        ->where('otp_code', $request->otp_code)
                        ->where('otp_expires_at', '>', now())  // التحقق من أن الكود لم ينتهِ صلاحيته
                        ->first();

        if (!$otpRecord) {
            // إذا لم يتم العثور على الكود أو انتهت صلاحيته
            return response()->json(['message' => 'الكود خطأ أو انتهت صلاحيته'], 400);
        }

        // هنا بنعتبر أن الكود تم التحقق منه بنجاح
        // مثلا: يمكنك الآن إرسال المستخدم إلى صفحة إعادة تعيين كلمة المرور
        // أو تعديل حالة التحقق في قاعدة البيانات إذا كنت تستخدم جدول آخر لتخزين حالة التحقق

        // في حالة استخدام جدول الـ users وتحديث حالة التحقق
        $user = User::where('email', $otpRecord->email)->first();

        if ($user) {
            // تحديث حالة التحقق
            $user->email_verified_at = now();
            $user->save();
        }

        return response()->json(['message' => 'تم التحقق من الكود بنجاح']);
    }
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return response()->json(["msg" => "Email already verified."], 400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return response()->json(["msg" => "Email verification link sent on your email id"]);
    }
}
