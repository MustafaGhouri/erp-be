<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserStatusUpdated;
use App\Http\Controllers\Controller;
use App\Models\AreaExpertiseOfUsers;
use App\Models\PasswordResets;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Pusher\Pusher;
use Tymon\JWTAuth\Facades\JWTAuth;

class AllAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register_with', 'login_with', 'logout', 'verify_email', 'resend_verify_email', 'reset_password', 'forgot_password']]);
    }
    public function connect(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;

        $redis = Redis::connection();

        // Add the user to the list of online users in Redis
        $redis->sadd('online_users', $user_id);

        $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
        );

        $pusher = new Pusher(
            'f003ced16e0ef4d30160',
            'c1a7f6f5d2b0d8a89962',
            '1644390',
            $options
        );

        // Get the updated list of online users from Redis
        $onlineUsers = $redis->smembers('online_users');

        // Trigger the event with the updated list of online users
        $pusher->trigger('my-channel', 'onlineUser', ['data' => $onlineUsers]);

        return response()->json(['message' => 'Connected successfully']);
    }

    public function disconnect(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->id;

        $redis = Redis::connection();

        // Remove the user from the list of online users in Redis
        $redis->srem('online_users', $user_id);

        $options = array(
            'cluster' => 'ap2',
            'useTLS' => true
        );

        $pusher = new Pusher(
            'f003ced16e0ef4d30160',
            'c1a7f6f5d2b0d8a89962',
            '1644390',
            $options
        );

        // Get the updated list of online users from Redis
        $onlineUsers = $redis->smembers('online_users');

        // Trigger the event with the updated list of online users
        $pusher->trigger('my-channel', 'onlineUser', ['data' => $onlineUsers]);

        return response()->json(['message' => 'Disconnected successfully']);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|min:2',
            "last_name" => 'required|min:2',
            "email" => 'required|email|unique:users',
            "password" => 'required|same:password_confirmation|min:6',
            "password_confirmation" => 'required',
            "role_id" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role_id" => $request->role_id,
            ]);
            $verification_code = $this->code(8, 15);
            User::where('id', $user->id)->update([
                "verification_code" => $verification_code
            ]);
            $this->mail("Verify Your Email And Active Your Account!", $request->first_name . " " . $request->last_name, $request->email, 'If this was you, please provide the below token on the challenge page:' . $verification_code);
            return response()->json([
                "res" => "success",
                "message" => "Your account has been created successfully! Please check your email inbox to verify your account. Do not forget to check your spam folder as well.",
                "users" => $this->user_detail($user->id),
            ]);
        }
    }

    public function update_client(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|min:2',
            "last_name" => 'required|min:2',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $imagename = "";
        $image = $request->file("image");
        if ($image != "" && $image != null) {
            $imagename =  time() . "-" . preg_replace("/[^.a-zA-Z0-9]/m", "-", $image->getClientOriginalName());
            $imagename = str_replace(" ", "", $imagename);
            $path = public_path() . '/uploads/users/';
            $image->move($path, $imagename);
        }
        User::where('id', auth()->user()->id)->update([
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "phone_number" => $request->phone_number ?? null,
            "address" => $request->address ?? null,
        ]);
        return response()->json([
            "res" => "success",
            "message" => "Your profile account all details update successfully!",
            "users" => $this->user_detail(auth()->user()->id),
        ]);
    }
    public function update_lawyer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|min:2',
            "last_name" => 'required|min:2',
            "country_id" => 'required',
            "state_id" => 'required',
            "phone_number" => 'required|min:10',
            "address" => 'required',
            "city" => 'required',
            "zip_code" => 'required',
            "image" => 'required|image|mimes:jpeg,png,jpg|max:2048',
            "short_bio" => 'max:500',
            "bar_membership_number" => 'required',
            "jurisdiction_id" => 'required',
            "area_expertise_id" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $imagename = "";
            $image = $request->file("image");
            if ($image != "" && $image != null) {
                $imagename =  time() . "-" . preg_replace("/[^.a-zA-Z0-9]/m", "-", $image->getClientOriginalName());
                $imagename = str_replace(" ", "", $imagename);
                $path = public_path() . '/uploads/users/';
                $image->move($path, $imagename);
            }
            $user = User::where('id', auth()->user()->id)->update([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "country_id" => $request->country_id ?? 0,
                "state_id" => $request->state_id ?? 0,
                "phone_number" => $request->phone_number ?? null,
                "address" => $request->address ?? null,
                "city" => $request->city ?? null,
                "zip_code" => $request->zip_code ?? null,
                "image" => $imagename ?? null,
                "short_bio" => base64_encode($request->short_bio) ?? null,
                "bar_membership_numer" => $request->bar_membership_number,
                "jurisdiction_id" => $request->jurisdiction_id,
            ]);
            $area_expertise_id = $request->input("area_expertise_id");
            if (count($area_expertise_id) > 0) {
                foreach ($area_expertise_id as $key => $value) {
                    AreaExpertiseOfUsers::create([
                        "user_id" => $user->id,
                        "area_expertise_id" => $value
                    ]);
                }
            }
            return response()->json([
                "res" => "success",
                "message" => "Your profile account all details update successfully!",
                "users" => $this->user_detail(auth()->id),
            ]);
        }
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|email',
            "password" => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::where('email', $request->email)->first();
            if (!empty($user) && $user->status == 0) {
                return response()->json(["res" => "error", "message" => "login failed because your account is not verified! Please check Your mail inbox!"]);
            }
            $token = Auth::attempt(['email' =>  $request->email, 'password' => $request->password, "status" => "1"]);

            if ($token) {
                User::where('id', auth()->user()->id)->update([
                    "is_online" => "1"
                ]);
                return response()->json([
                    "res" => "success",
                    "message" => "Login Successfully!",
                    "role_id" => auth()->user()->role_id,
                    "token" => $this->respondWithToken($token),
                    "user" => $this->user_detail(auth()->user()->id)
                ]);
            } else {
                return response()->json(["message" => "Incorrect Email and Password!", "res" => "warning"]);
            }
        }
    }
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
    public function refresh()
    {
        return response()->json([
            "res" => "success",
            "message" => "Refresh Token Create Successfully!",
            "token" => $this->respondWithToken(auth()->refresh())
        ]);
    }
    public function logout()
    {
        User::where('id', Auth::id())->update([
            "is_online" => "0"
        ]);
        auth()->logout();
        return response()->json(["res" => "success", 'message' => 'User successfully signed out']);
    }
    public function verify_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::where('verification_code', $request->token)->first();
            if (empty($user)) {
                return response()->json(["res" => "error", 'message' => 'This account activation token is invalid.']);
            }
            $time = Carbon::parse($user->updated_at)->addMinutes(10)->toDateTimeString();
            if (now()->toDateTimeString() >= $time) {
                return response()->json(["res" => "error", 'message' => 'Token valid timeout, Please generate new token.']);
            }
            if ($user->status == "1" and !empty($user->email_verified_at)) {
                return response()->json(["res" => "warning", 'message' => 'Your account already activated. Please do a login.']);
            }
            User::where('id', $user->id)->update([
                'status' => '1',
                'email_verified_at' => now()
            ]);

            $user = User::where('verification_code', $request->token)
                ->where('id', $user['id'])
                ->where('status', '1')
                ->first();

            if (empty($user)) {
                return response()->json(["res" => "error", 'message' => 'This account activation token is invalid.']);
            }

            // Generate the JWT token for the user
            $token = JWTAuth::fromUser($user);

            if ($token) {
                User::where('id', $user['id'])->update([
                    "is_online" => "1"
                ]);
                return response()->json([
                    "res" => "success",
                    "message" => 'Your account is successfully activated.Login Successfully!',
                    "role_id" => $user['role_id'],
                    "token" => $this->respondWithToken($token), // Include the JWT token in the response
                    "user" => $this->user_detail($user['id'])
                ]);
            }
        }
    }
    public function resend_verify_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::where('email', $request->email)->first();
            $time = Carbon::parse($user->updated_at)->addMinutes(10)->toDateTimeString();
            if (empty($user)) {
                return response()->json(["res" => "error", 'message' => 'User Not Found!']);
            }
            if ($user->status == "1" and !empty($user->email_verified_at)) {
                return response()->json(["res" => "warning", 'message' => 'Your account already activated. Please do a login.']);
            }
            // Check if a token was sent within the last 10 minutes
            $lastTokenSentAt = $user->updated_at;
            $cooldownPeriod = Carbon::parse($lastTokenSentAt)->addMinutes(10);
            if (Carbon::now()->lt($cooldownPeriod)) {
                $remainingTime = Carbon::now()->diffInSeconds($cooldownPeriod);
                return response()->json(["res" => "error", 'message' => 'A new verification token can only be sent after ' . $remainingTime . ' seconds.']);
            }
            // Generate a new verification token
            $verification_code = $this->code(8, 15);
            // A success response
            User::where('id', $user->id)->update([
                "verification_code" => $verification_code,
                "updated_at" => now()
            ]);
            $this->mail("Email Verification Token Again Sent!", $user->first_name . " " . $user->last_name, $request->email, 'If this was you, please provide the below token on the challenge page:' . $verification_code);
            return response()->json(["res" => "success", 'message' => 'New Verification Token Has Been Sent To Your Email Account!']);
        }
    }
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|email'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $user = User::where('email', $request->email)->first();
            if (empty($user)) {
                return response()->json(["res" => "error", 'message' => 'User Not Found!']);
            }
            // Generate a new verification token
            $verification_code = $this->code(8, 15);
            // A success response
            PasswordResets::unguard();
            PasswordResets::create([
                "email" => $request->email,
                "token" => $verification_code
            ]);
            PasswordResets::reguard();
            $this->mail("Reset Password Code Sent!", $user->first_name . " " . $user->last_name, $request->email, 'If this was you, please provide the below token on the challenge page:' . $verification_code);
            return response()->json(["res" => "success", 'message' => 'A reset password token has been sent to you, Please check your email inbox. Do not forget to check your spam folder as well.']);
        }
    }
    public function forgot_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "token" => 'required',
            'password' => 'required|same:password_confirmation|min:6',
            "password_confirmation" => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $resetpassword = PasswordResets::where('token', $request->token)->first();
            if ($resetpassword) {
                $user = User::where('email', $resetpassword->email)->first();
                if (empty($user)) {
                    return response()->json(["res" => "error", 'message' => 'User Not Found!']);
                }
                // A success response
                User::where('id', $user->id)->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => now()
                ]);
                return response()->json(["res" => "success", 'message' => 'Password Change Successfully!']);
            } else {
                return response()->json(["res" => "error", 'message' => 'Reset Password Code Not Valid!']);
            }
        }
    }

    public function user_detail($id = null)
    {
        $data = array();
        if (!Auth::check()) {
            $user = User::find($id);
        } else {
            $user = User::find(auth()->user()->id);
        }
        if (!empty($user)) {




            $data = array(
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "role" => $user->role->name,
                "email" => $user->email,
                "status" => ($user->status == 1) ? "Active" : "Inactive",
                "phone_number" => $user->phone_number,
                "region" => $user->rigion,
            );

            return $data;
        }
    }
    public function mail($subject, $name, $email, $message)
    {
        $user = new User();
        $user->subject = $subject;
        $user->greeting = "Hello " . $name . ",";
        $user->email = $email;
        $user->verificationUrl = $message;
        $user->notify(new VerifyEmailNotification($user));
    }
    public function code($min, $max)
    {
        $length = mt_rand($min, $max); // Generate a random length between min and max
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // Characters to use for generating the random string
        $randomString = Str::upper(Str::random($length)); // Generate a random string of the specified length and convert it to uppercase
        return $randomString;
    }
}
