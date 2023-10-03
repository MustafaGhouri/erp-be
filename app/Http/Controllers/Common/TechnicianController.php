<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TechnicianController extends Controller
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
                "phone" => 'required',
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
                'phone' => $request->phone,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Technician successfully created ",
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
            $technicians = User::with(['region_detail'])->where('role_id', '2')->orderBy('id', 'DESC')->get();

            foreach ($technicians as $technician) {
                array_push($data, [
                    'id' => $technician->id,
                    'first_name' => $technician->first_name,
                    'last_name' => $technician->last_name,
                    'email' => $technician->email,
                    'phone' => $technician->phone,
                    'status' => $technician->status == 1 ? 'Active' : 'In-Active',
                    'region' => [
                        'id' => $technician->region_detail->id,
                        'name' => $technician->region_detail->name,
                    ],

                ]);
            }


            return response()->json(['status' => 'success', 'message' => 'Successfully technicians retrieved', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "warning",
                "message" => "Something wrong. Try again! ",
                'error' => $e->getMessage()
            ]);
        }
    }
}
