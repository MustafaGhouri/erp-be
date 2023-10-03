<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RequesterController extends Controller
{
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "first_name" => 'required|min:2',
                "last_name" => 'required|min:2',
                "email" => 'required|email|unique:users',
                "password" => 'required|min:6',
                "region" => 'required',
                "location" => 'required',
                "customer" => 'required',
                "status" => 'required',
                "role_id" => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }
            $user = User::create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role_id" => $request->role_id,
                'status' => $request->status,
                'region' => $request->region,
                'location' => $request->location,
                'customer' => $request->customer,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Requester successfully created ",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "warning",
                "message" => "Something wrong. Try again! ",
                'error' => $e->getMessage()
            ]);
        }
    }
}
