<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductModelController extends Controller
{

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validator = Validator::make($request->all(), [
                "name" => 'required|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", "message" => $validator->errors()]);
            }

            $unit = ProductModel::create([
                'name' => $request->name,
                'brand' => $request->brand,
                'user_id' => $user_id,
            ]);
            return response()->json(['status' => 'success', 'message' => 'Product Model stored successfully', 'data' => $unit]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while storing the data', 'error' => $e->getMessage()]);
        }
    }

    public function bulk_store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;

            // Validate the request to ensure it contains a CSV file
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|mimes:csv,txt',
                'brand' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }

            // Get the uploaded CSV file
            $csvFile = $request->file('csv_file');

            // Read and process the CSV data
            $csvData = array_map('str_getcsv', file($csvFile));

            // Initialize an array to store the data to be inserted
            $dataToInsert = [];

            foreach ($csvData as $row) {
                // Assuming your CSV has a 'name' column
                $name = $row[0]; // Adjust the index as needed for your CSV structure


                $dataToInsert[] = [
                    'name' => $name,
                    'brand' => $request->brand,
                    'user_id' => $user_id,
                ];
            }

            // Insert the data in bulk
            ProductModel::insert($dataToInsert);

            return response()->json(['status' => 'success', 'message' => 'Product Models stored successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }

    public function list()
    {
        try {
            $data = [];
            $modelsQuery = ProductModel::with(['brandDetails'])->orderBy('id', 'desc')->get();
            $models = $modelsQuery;
            if (empty($models)) {
                return response()->json(['status' => 'warning', 'message' => 'Product Model not found', 'data' => []]);
            }

            foreach ($models as $model) {
                $brands = $model->brand;
                array_push($data, [
                    'id' => $model->id,
                    'name' => $model->name,
                    'brand_id' => $model->brandDetails->id,
                    'brand_name' => $model->brandDetails->name
                ]);
            }


            return response()->json(['status' => 'success', 'message' => 'Product Model successfully retrieved', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
    public function list_by_brands($id)
    {
        try {

            $data = ProductModel::where('brand', $id)->orderBy('id', 'desc')->get();
            if (empty($data)) {
                return response()->json(['status' => 'warning', 'message' => 'Product Model not found', 'data' => []]);
            } 

            return response()->json(['status' => 'success', 'message' => 'Product Model successfully retrieved', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
