<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function UserRegistration(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json(['status' => 'success','message' => 'User registered successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed','error' => $e->getMessage()], 400);
        }
    }

    public function UserLogin(Request $request)
    {
        try {

            $request->validate([
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !password_verify($request->password, $user->password)) {
                return response()->json(['status' => 'failed','message' => 'Invalid credentials'], 401);
            }

            $token = JWTToken::generateToken($user);

            return response()->json(['status' => 'success','message' => 'User logged in successfully', 'token' => $token], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed','error' => $e->getMessage()], 400);
        }
    }

    public function SendOTPMail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['status' => 'failed','message' => 'User not found'], 404);
            }

            // Generate OTP and send email logic here
            $otp = rand(1000, 9999);
            Mail::to($user->email)->send(new SendOTPMail($otp));
            User::where('email', $request->email)->update(['otp' => $otp]);
            return response()->json(['status' => 'success','message' => 'OTP sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed','error' => $e->getMessage()], 400);
        }
    }
}
