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
            'first_name' => 'required',
            'last_name' => 'required',
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
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,

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

    public function login(Request $request)
    {
        try
        {
            $validateUser = Validator::make($request->all(),
[

            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                    'status'=> false,
                    'message'=> 'Validation error',
                    'errors'=> $validateUser->errors()

            ],401);
        }

        if(!Auth::attempt($request->only(['email','password']))){
            return response()->json([
                'status'=> false,
                'message'=> 'Email & Password does not match with our record.',

        ],401);

        }

        $user = User::where('email', $request->email)->first();
        return response()->json([
            'status' => true,
            'message' => 'User Logged In successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 201);

        }catch (\Throwable $th){
            return response()->json([
                'status'=> false,
                'message'=> $th-> getMessage(),
            ],500);

        }

    }



    public function Logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'status'=> true,
            'message'=> 'User Logged Out',
            'data'=> [],

        ],201);

    }


}
