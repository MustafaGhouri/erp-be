<?php

namespace App\Http\Controllers\AppControllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Printer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        try {
            $requester = auth()->user();
            $requester_id = $requester->id;

            $validator = Validator::make($request->all(), ([
                'complaint_category' => 'required',
                'problem' => 'required',
                'priority' => 'required',
                'printer' => 'required',
            ]));

            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }

            $printer = Printer::find($request->printer);
            if (empty($printer)) {
                return response()->json(['status' => 'warning', 'message' => 'Printer not found']);
            }

            $imagename = "";
            $image = $request->file("image");
            if ($image != "" && $image != null) {
                $fileExtension = $image->getClientOriginalExtension();
                $imagename = 'GCS-CMS-' . time() . '.' . $fileExtension;
                // Replace any spaces in the image name
                $imagename = str_replace(" ", "", $imagename);
                $path = public_path() . '/uploads/complaints/';
                $image->move($path, $imagename);
            }

            $complaint = Complaint::create([
                'complain_category' => $request->complaint_category,
                'problem' => $request->problem,
                'priority' => $request->priority,
                'printer' => $printer->id,
                'description' => $request->description,
                'region' => $printer->region,
                'customer' => $printer->customer,
                'location' => $request->location,
                'department' => $request->department,
                'requester' => $requester_id,
                'screenshot' => $imagename,
                'status' => 'unAssign',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Complaint submitted successfully.', 'data' => $complaint]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the complaint', 'error' => $e->getMessage()]);
        }
    }
    
}