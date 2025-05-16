<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

     function LoginPage()
    {
        return view('pages.auth.login-page');
    }

    function RegistrationPage()
    {
        return view('pages.auth.registration-page');
    }
    function SendOtpPage()
    {
        return view('pages.auth.send-otp-page');
    }
    function VerifyOTPPage()
    {
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage()
    {
        return view('pages.auth.reset-pass-page');
    }

    function ProfilePage()
    {
        return view('pages.dashboard.profile-page');
    }
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

            return response()->json(['status' => 'success','message' => 'User logged in successfully'], 200)
                ->cookie('token', $token, time() + 60 * 24 * 30);
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

    public function VerifyOTPMail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:255',
                'otp' => 'required|integer|digits:4',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['status' => 'failed','message' => 'User not found'], 404);
            }

            if ($user->otp != $request->otp) {
                return response()->json(['status' => 'failed','message' => 'Invalid OTP'], 401);
            }
            if($user->updated_at->diffInMinutes() > 5){
                return response()->json(['status' => 'failed','message' => 'OTP expired'], 401);
            }


            // OTP verified successfully
            User::where('email', $request->email)->update(['otp' => '0']); // Clear OTP after verification
            $token = JWTToken::createVerifyToken($user);

            return response()->json(['status' => 'success','message' => 'OTP verified successfully', 'token' => $token], 200)
                            ->cookie('token', $token, time() + 60 * 24 * 30);
            
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed','error' => $e->getMessage()], 400);
        }
    }

    public function ResetPassword(Request $request)
    {
        try {
            $request->validate([
                // 'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);
            // dd($request->all());

            $email = $request->header('email');
            User::where('email', $email)->update(['password' => bcrypt($request->password)]);
            return response()->json(['status' => 'success','message' => 'Password reset successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'failed','error' => $e->getMessage()], 400);
        }
    }

    function UserLogout(){
        return redirect('/')->cookie('token','',-1);
    }


    function UserProfile(Request $request){
        $email=$request->header('email');
        $user=User::where('email','=',$email)->first();
        return response()->json([
            'status' => 'success',
            'message' => 'Request Successful',
            'data' => $user
        ],200);
    }

    function UpdateProfile(Request $request){
        try{
            $email=$request->header('email');
            $firstName=$request->input('firstName');
            $lastName=$request->input('lastName');
            $mobile=$request->input('mobile');
            $password=$request->input('password');
            User::where('email','=',$email)->update([
                'firstName'=>$firstName,
                'lastName'=>$lastName,
                'mobile'=>$mobile,
                'password'=>$password
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Request Successful',
            ],200);

        }catch (Exception $exception){
            return response()->json([
                'status' => 'fail',
                'message' => 'Something Went Wrong',
            ],200);
        }
    }
}
