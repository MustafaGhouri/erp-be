<?php

namespace App\Http\Controllers\AppControllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintsController extends Controller
{
    public function list()
    {
        try {
            $user  = auth()->user();

            $data = [];
            $printer_data = [];

            $records = Complaint::where('region', $user->region)->where('status', 'unAssign')->orderBy('id', 'DESC')->get();
            foreach ($records as $record) {
                $printer = Printer::with(['brand_detail', 'model_detail', 'customer_detail', 'location_detail', 'department_detail'])->where('id', $record->printer)->first();

                $printer_data = [
                    'id' => $printer->id,
                    'name' => $printer->name,
                    'brand' => $printer->brand_detail->name,
                    'model' => $printer->model_detail->name,
                ];


                array_push($data, [
                    'id' => $record->id,
                    'complain_category' => $record->complain_category,
                    'problem' => $record->problem,
                    'status' => $record->status,
                    'priority' => $record->priority,
                    'created_at' => date('d-m-Y', strtotime($record->created_at)),
                    'printer' => $printer_data,
                    'customer' => $printer->customer_detail != null ? $printer->customer_detail->name : '',
                    'location' => $printer->location_detail != null ? $printer->location_detail->name : '',
                    'department' => $printer->department_detail != null ? $printer->department_detail->name : '',
                ]);
            }

            return response()->json(['status' => 'success', 'meesage' => 'Records successfully found', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the complaint', 'error' => $e->getMessage()]);
        }
    }

    public function assignComplaints($id)
    {
        try {
            $user = auth()->user();
            $complaint = Complaint::where('status', 'unAssign')->where('id', $id)->first();
            if (empty($complaint)) {
                return response()->json(['status' => 'alreadyAssigned', 'message' => 'Already assigned to someone']);
            }
            $complaint->update([
                'tech' => $user->id,
                'status' => 'inProgress',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Successfully Assigned']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'warning', 'message' => 'Something wrong, please try again!']);
        }
    }
    public function completeComplaint(Request $request)
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

            $complaint = Complaint::where('status', 'inProgress')->where('id', $request->id)->first();
            if (empty($complaint)) {
                return response()->json(['status' => 'alreadyAssigned', 'message' => 'Already completed to someone']);
            }

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


            $complaint->update([
                'status' => 'completed',
                'counter' => $request->counter,
                'counter_file' => $imagename,
                'complete_date' => now(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Successfully Completed']);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'warning', 'message' => 'Something wrong, please try again!']);
        }
    }
}
