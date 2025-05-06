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

            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $folderPath = public_path('avatars');
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            $file->move($folderPath, $filename);


            $data['avatar'] = 'avatars/' . $filename;
        }

        $user->update($data);

        
        $userData = $user->toArray();
        if ($user->avatar) {
            $userData['avatar'] = asset($user->avatar);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $userData,
        ]);
    }
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        if ($user->avatar && File::exists(public_path($user->avatar))) {
            File::delete(public_path($user->avatar));
        }

        $folderPath = public_path('avatars');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $file = $request->file('avatar');
        $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();


        $file->move($folderPath, $filename);

        $user->avatar = 'avatars/' . $filename;
        $user->save();


        return response()->json([
            'message' => 'Profile image updated successfully',
            'avatar' => asset($user->avatar),
        ]);
    }

}
