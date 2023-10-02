<?php

namespace App\Http\Controllers\AppControllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
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
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }
}
