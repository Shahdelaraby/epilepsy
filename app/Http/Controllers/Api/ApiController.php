<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ApiController extends Controller
{
    public function register(Request $request)
    {
        try
        {
        $validateUser = Validator::make($request->all(),
[

            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required'

        ]);

        if ($validateUser->fails()) {
            return response()->json([
                    'status'=> false,
                    'message'=> 'Validation error',
                    'errors'=> $validateUser->errors()

            ],401);

        }
        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            // 'password_confirmation' => $request->password_confirmation,

        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 201);


        }catch (\Throwable $th){
        return response()->json([
            'status'=> false,
            'message'=> $th-> getMessage(),
        ],500);

        }
    }


    //Login
    //public function login(Request $request)
   // {
      //  try
       // {
          //  $validateUser = Validator::make($request->all(),
//[

        //    'email' => 'required|email',
         //   'password' => 'required'
       // ]);

       // if ($validateUser->fails()) {
           // return response()->json([
             //       'status'=> false,
               //     'message'=> 'Validation error',
                 //   'errors'=> $validateUser->errors()

           // ],401);
        //}

       // if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
         //   return response()->json([
          //      'status'=> false,
          //      'message'=> 'Email & Password does not match with our record.',

       // ],401);

       // }

       // $user = User::where('email', $request->email)->first();
       // return response()->json([
        //    'status' => true,
         //   'message' => 'User Logged In successfully',
         //   'token' => $user->createToken("API TOKEN")->plainTextToken
       // ], 201);

       // }catch (\Throwable $th){
        //    return response()->json([
        //        'status'=> false,
          //      'message'=> $th-> getMessage(),
            //],500);

       // }

    //}

    public function login(Request $request)
{
    try {
        // التحقق من صحة البيانات المدخلة
        $validateUser = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // إذا كانت البيانات المدخلة غير صحيحة، إرجاع خطأ مع تفاصيل
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
            ], 401);
        }

        // محاولة التحقق من البيانات المدخلة في قاعدة البيانات
        if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email & Password do not match with our records.',
            ], 401);
        }

        // جلب المستخدم بناءً على البريد الإلكتروني
        $user = Auth::guard('web')->user(); // تم استخدام Auth هنا بدلاً من User::where

        // توليد توكن للمستخدم
        $token = $user->createToken('API Token')->plainTextToken;

        // جلب البروفايل المرتبط باليوزر
        $profile = $user->profile; // Assuming there's a 'profile' relationship

        // إرجاع استجابة ناجحة مع بيانات اليوزر والبروفايل
        return response()->json([
            'status' => true,
            'message' => 'User Logged In successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null, // تأكد من أن اليوزر عنده صورة (avatar)
                'profile' => $profile ? [
                    'bio' => $profile->bio,
                    'phone' => $profile->phone,
                    'address' => $profile->address,
                ] : null,
            ],
        ], 201);
    } catch (\Throwable $th) {
        // في حالة حدوث أي استثناء، إرجاع خطأ مع تفاصيل
        return response()->json([
            'status' => false,
            'message' => $th->getMessage(),
        ], 500);
    }
}


    //Logout the authenticated user

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=> true,
            'message'=> 'User Logged Out',
            'data'=> [],

        ],201);
}





}
