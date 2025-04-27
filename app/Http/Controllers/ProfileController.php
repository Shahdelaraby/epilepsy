<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // تعديل avatar عشان يرجع لينك كامل
        $userData = $user->toArray();
        if ($user->avatar) {
            $userData['avatar'] = asset($user->avatar);
        }

        return response()->json($userData);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'country' => 'nullable|string|max:100',
            'gender' => 'nullable|in:Male,Female',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة لو موجودة
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            // تأكد إن الفولدر موجود
            $folderPath = public_path('avatars');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            // اسم فريد للصورة
            $file = $request->file('avatar');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // حفظ الصورة في public/avatars
            $file->move($folderPath, $filename);

            // حفظ المسار النسبي في الداتابيز
            $data['avatar'] = 'avatars/' . $filename;
        }

        $user->update($data);

        // نرجع رابط الصورة كامل
        $userData = $user->toArray();
        if ($user->avatar) {
            $userData['avatar'] = asset($user->avatar);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $userData,
        ]);
    }
}
