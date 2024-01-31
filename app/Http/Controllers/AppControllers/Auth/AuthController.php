<?php

namespace App\Http\Controllers\AppControllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "email" => 'required|email',
                "password" => 'required|min:6'
            ]);
            if ($validator->fails()) {
                $errorString = implode(' , ', $validator->errors()->all());
                return response()->json(['res' => 'warning', 'message' => 'Validation Error: ' . $errorString, 'error' => $errorString]);
            }

            $user = User::where('email', $request->email)->first();
            if (!empty($user) && $user->status == 0) {
                return response()->json(["res" => "warning", "message" => "login failed because your account is not verified! Please check Your mail inbox!"]);
            }
            if ($user->role_id == 2 || $user->role_id == 3) {
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
            } else {

                return response()->json(["message" => "Only tech and client allowed", "res" => "warning"]);
            }

            //code...
        } catch (\Throwable $th) {
            return response()->json(["message" => "Only tech and client allowed", "res" => "error", 'error' => $th->getMessage()]);
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
