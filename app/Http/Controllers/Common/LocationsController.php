<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationsController extends Controller
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
                    'region' => $request->region,
                    'user_id' => $user_id,
                    'created_at' => now(),
                ];
            }

            // Insert the data in bulk
            Location::insert($dataToInsert);

            return response()->json(['status' => 'success', 'message' => 'Locations stored successfully']);
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

            $location =  Location::create([
                'name' => $request->name,
                'region' => $request->region,
                'user_id' => $user_id,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Location stored successfully', 'data' => $location]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }
    public function list_by_region($id)
    {
        try {
            $data = [];
            $locations =  Location::where('region', $id)->orderBy('id', 'DESC')->get();

            if (count($locations) == 0) {
                return response()->json(['status' => 'success', 'message' => "No data found", 'data' => []]);
            }

            foreach ($locations as $location) {
                array_push($data, [
                    'id' => $location->id,
                    'name' => $location->name,
                ]);
            }
            return response()->json(['status' => 'success', 'message' => "Locations successfully found", 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 'warning', 'message' => "Something wrong while retrieved the data", 'error' => $e->getMessage()]);
        }
    }
    public function list()
    {
        try {

            $location = Location::orderBy('id', 'desc')->get();
            if (count($location) == 0) {
                return response()->json(['status' => 'warning', 'message' => 'Locations not found', 'data' => []]);
            }

            return response()->json(['status' => 'success', 'message' => 'Locations successfully retrieved', 'data' => $location]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {

        try {
            Location::destroy($id);
            return response()->json(['status' => 'success', 'message' => 'Locations successfully destroy']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while destroing the data', 'error' => $e->getMessage()]);
        }
    }
}
