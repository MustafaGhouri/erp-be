<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Auth\AllAuthController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where('role_id', 2)->get();
        if (!empty($admins)) {
            return response()->json(["res" => "success", "message" => "Admin List", "data" => $admins]);
        } else {
            return response()->json(["res" => "error", "message" => "Admins Not Found!"]);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|min:2',
            "last_name" => 'required|min:2',
            "email" => 'required|email|unique:users',
            "password" => 'required|same:password_confirmation|min:6',
            "password_confirmation" => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            $admin = User::create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role_id" => 2,
                "terms_of_service" => 1,
                "status" => $request->status,
                "iban" => null,
                "balance" => null,
            ]);
            $this->mail("Account Created & Your Login Details Here!", $request->first_name . " " . $request->last_name, $request->email, "Your Login Email and Password Here:<br> Email Address:" . $request->email . "<br> Password:" . $request->password);
            if (!empty($admin)) {
                return response()->json([
                    "res" => "success",
                    "message" => "Admin Added Successfully!",
                ]);
            } else {
                return response()->json([
                    "res" => "error",
                    "message" => "Can Not Add Admin!",
                ]);
            }
        }
    }
    public function show(string $id)
    {

        $admin = User::find($id);
        if (!empty($admin)) {
            return response()->json([
                "res" => "success",
                "message" => "Admin Found!",
                "detail" => $admin
            ]);
        } else {
            return response()->json([
                "res" => "error",
                "message" => "Admin Not Found!",
            ]);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            "first_name" => 'required|min:2',
            "last_name" => 'required|min:2',
            "email" => 'required|email',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } else {
            if ($request->password != "") {
                User::where('id', $id)->update([
                    "password" => Hash::make($request->password),
                ]);
            }
            $admin = User::where('id', $id)->update([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "status" => $request->status
            ]);
            if (!empty($admin)) {
                return response()->json([
                    "res" => "success",
                    "message" => "Admin Updated Successfully!",
                ]);
            } else {
                return response()->json([
                    "res" => "error",
                    "message" => "Can Not Update Admin!",
                ]);
            }
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return response()->json([
            "res" => "success",
            "message" => "Admin Deleted Successfully!",
        ]);
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
}
