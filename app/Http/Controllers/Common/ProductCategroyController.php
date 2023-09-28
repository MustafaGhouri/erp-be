<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategroyController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validator = Validator::make($request->all(), [
                "name" => 'required|min:4|unique:product_category',
            ], [
                "name.unique" => "The product Category has already been taken.",
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", "message" => 'Name should be unique and min 4 characters', $validator->errors()]);
            }

            $create =  ProductCategory::create([
                'name' => $request->name,
                'user_id' => $user_id
            ]);


            return response()->json(['status' => 'success', 'message' => 'Product category stored successfully', 'data' => $create]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while storing the data', 'error' => $e->getMessage()]);
        }
    }

    public function list()
    {
        try {

            $data = ProductCategory::orderBy('id', 'desc')->get();
            if (!empty($data)) {

                return response()->json(['status' => 'success', 'message' => 'Product category successfully retrieved', 'data' => $data]);
            }
            return response()->json(['status' => 'warning', 'message' => 'Product category not found', 'data' => []]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name" => 'required|min:4|unique:product_category',
            ], [
                "name.unique" => "The product Category has already been taken.",
            ]);

            $data = ProductCategory::where('id', $id)->update([
                'name' => $request->name
            ]);

            return response()->json(['status' => 'success', 'message' => 'Product category successfully updated']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while updating the data', 'error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            ProductCategory::destroy($id);
            return response()->json(['status' => 'success', 'message' => 'Product category successfully destroed']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
