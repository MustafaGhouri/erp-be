<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validator = Validator::make($request->all(), [
                "name" => 'required|min:4|unique:suppliers',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", "message" => $validator->errors()]);
            }
            $supplier = Supplier::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'country' => $request->country,
                'email' => $request->email,
                'description' => $request->description,
                'user_id' => $user_id,
            ]);

            return response()->json(['status' => "success", "message" =>  'Supplier successfully stored', 'data' => $supplier]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }
}
