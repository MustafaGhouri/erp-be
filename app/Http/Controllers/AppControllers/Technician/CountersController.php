<?php

namespace App\Http\Controllers\AppControllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\CounterLog;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountersController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                "counter" => 'required',
                "file" => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }
            $printer = Printer::where('id', $request->id)->first();

            $imagename = "";
            $image = $request->file("file");
            if ($image != "" && $image != null) {
                $fileExtension = $image->getClientOriginalExtension();
                $imagename = 'GCS-counters-' . $request->id . '-' . time() . '.' . $fileExtension;
                // Replace any spaces in the image name
                $imagename = str_replace(" ", "", $imagename);
                $path = public_path() . '/uploads/counters/';
                $image->move($path, $imagename);
            }

            CounterLog::create([
                'printer' => $printer->id,
                'user_id' => $user->id,
                'before_counter' => $printer->counter,
                'counter' => $request->counter,
                'counter_file' => $imagename,
                'log_type' => 'counter',
                'region' => $printer->region,
                'customer' => $printer->customer,
                'location' => $printer->location,
                'department' => $printer->department,
            ]);

            Printer::where('id', $printer->id)->update([
                'counter' => $request->counter
            ]);
            return response()->json(['status' => 'success', 'message' => 'Counter successfully updated']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something wrong, please try again!', 'error' => $e->getMessage()]);
        }
    }
}
