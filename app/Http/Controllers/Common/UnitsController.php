<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitsController extends Controller
{
    public function store(Request $request)
    {

        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validator = Validator::make($request->all(), [
                "name" => 'required|min:4|unique:product_category',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", "message" => $validator->errors()]);
            }

            $unit = Unit::create([
                'name' => $request->name,
                'user_id' => $user_id,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Unit stored successfully', 'data' => $unit]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while storing the data', 'error' => $e->getMessage()]);
        }
    }


    public function list()
    {
        try {

            $data = Unit::orderBy('id', 'desc')->get();
            if (!empty($data)) {

                return response()->json(['status' => 'success', 'message' => 'Unit successfully retrieved', 'data' => $data]);
            }
            return response()->json(['status' => 'warning', 'message' => 'Unit not found', 'data' => []]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
