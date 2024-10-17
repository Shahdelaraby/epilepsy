<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller {

    public function register(Request $request) {
        $validator = $this->validateUser($request);
        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return $this->successResponse('User created successfully please verify your email', [], 201);
    }

    // Login an existing user
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        // Check if email and password match
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', [], 401);
        }

        return $this->successResponse('User logged in successfully', [
            'token' => $user->createToken('API TOKEN')->plainTextToken,
        ]);
    }

    // Logout the authenticated user
    public function logout() {
        auth()->user()->tokens()->delete();

        return $this->successResponse('User logged out successfully', []);
    }

    // Send a test email
    public function sendTestEmail() {
        Mail::raw('This is a test email', function ($message) {
            $message->to('rania@gmail.com')
                    ->subject('Test Email');
        });

        return $this->successResponse('Email sent successfully!');
    }

    // Helper method for user registration validation
    private function validateUser(Request $request) {
        return Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email|max:255',
            'password'   => 'required|confirmed|min:8',
        ]);
    }

    // Helper method for sending error responses
    private function errorResponse($message, $errors = [], $status = 400) {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    // Helper method for sending success responses
    private function successResponse($message, $data = [], $status = 200) {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }
}




