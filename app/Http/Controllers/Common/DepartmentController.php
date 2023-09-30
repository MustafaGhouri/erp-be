<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function bulk_store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;

            // Validate the request to ensure it contains a CSV file
            $validator = Validator::make($request->all(), [
                'csv_file' => 'required|file|mimes:csv,txt',
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
                    'region_id' => 0,
                    'user_id' => $user_id,
                    'created_at' => now(),
                ];
            }

            // Insert the data in bulk
            Department::insert($dataToInsert);

            return response()->json(['status' => 'success', 'message' => 'Departments stored successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;

            // Validate the request to ensure it contains a CSV file
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }

            $department =  Department::create([
                'name' => $request->name,
                'region_id' => 0,
                'user_id' => $user_id,
                'created_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Department stored successfully', 'data' => $department]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }
    public function list()
    {
        try {

            $department = Department::orderBy('id', 'desc')->get();
            if (count($department) == 0) {
                return response()->json(['status' => 'warning', 'message' => 'Departments not found', 'data' => []]);
            }

            return response()->json(['status' => 'success', 'message' => 'Departments successfully retrieved', 'data' => $department]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {

        try {
            Department::destroy($id);
            return response()->json(['status' => 'success', 'message' => 'Departments successfully destroy']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while destroing the data', 'error' => $e->getMessage()]);
        }
    }
}
