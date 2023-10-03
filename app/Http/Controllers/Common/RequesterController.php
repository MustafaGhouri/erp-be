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

    public function list()
    {
        try {
            $data = [];
            $requesters = User::with(['region_detail', 'customer_detail', 'location_detail', 'department_detail'])->where('role_id', '3')->orderBy('id', 'DESC')->get();

            foreach ($requesters as  $requester) {
                array_push($data, [
                    'id' => $requester->id,
                    'first_name' => $requester->first_name,
                    'last_name' => $requester->last_name,
                    'email' => $requester->email,
                    'status' => $requester->status == 1 ? 'Active' : 'In-Active',
                    'region' => [
                        'id' => $requester->region_detail->id,
                        'name' => $requester->region_detail->name,
                    ],
                    'customer' => [
                        'id' => $requester->customer_detail->id,
                        'name' => $requester->customer_detail->name,
                    ],
                    'location' => [
                        'id' => $requester->location_detail->id,
                        'name' => $requester->location_detail->name,
                    ],
                    'department' => [
                        'id' => $requester->department_detail->id,
                        'name' => $requester->department_detail->name,
                    ],
                ]);
            }


            return response()->json(['status' => 'success', 'message' => 'Successfully requesters retrieved', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "warning",
                "message" => "Something wrong. Try again! ",
                'error' => $e->getMessage()
            ]);
        }
    }
}
