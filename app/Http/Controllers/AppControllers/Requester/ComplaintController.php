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
            if (!auth()->check()) {
                return response()->json(['status' => 'warning', 'message' => 'User not found']);
            }
            $requester = auth()->user();
            $requester_id = $requester->id;

            $validator = Validator::make($request->all(), ([
                'complain_category' => 'required',
                'problem' => 'required',
                'priority' => 'required',
                'printer' => 'required',
            ]));

            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }

            $printer = Printer::where('id', $request->printer)->first();
            if (empty($printer) || $printer == null) {
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
                'complain_category' => $request->complain_category,
                'problem' => $request->problem,
                'priority' => $request->priority,
                'printer' => $printer->id,
                'description' => $request->description,
                'region' => $printer->region,
                'customer' => $printer->customer,
                'location' => $printer->location,
                'department' => $printer->department,
                'requester' => $requester_id,
                'screenshot' => $imagename,
                'status' => 'pending',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Complaint submitted successfully.', 'data' => $complaint]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the complaint', 'error' => $e->getMessage()]);
        }
    }
    public function list($date)
    {
        try {
            $user  = auth()->user();
            $user_id = $user->id;
            $monthYear = $date;
            $data = [];
            $printer_data = [];
            if ($user->role_id == 3) {
                $records = Complaint::whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$monthYear])->where('requester', $user->id)->orderBy('id', 'DESC')->get();
            } else if ($user->role_id == 2) {
                $records = Complaint::whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$monthYear])->where('tech', $user->id)->orderBy('id', 'DESC')->get();
            }

            foreach ($records as $record) {

                $printer = Printer::with(['brand_detail', 'model_detail'])->where('id', $record->printer)->first();

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
                ]);
            }

            return response()->json(['status' => 'success', 'meesage' => 'Records successfully found', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the complaint', 'error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {

            $record = Complaint::where('id', $id)->first();
            $printer = Printer::with(['region_detail', 'customer_detail', 'location_detail', 'department_detail', 'brand_detail', 'model_detail', 'user_detail'])->where('id', $record->printer)->first();

            if (empty($printer)) {
                return response()->json(['status' => 'warning', 'message' => 'Printer not found', 'data' => []]);
            }

            $data = [
                'id' => $printer->id,
                'name' => $printer->name,
                'serial_number' => $printer->serial_number,
                'counter' => $printer->counter,
                'qrcodes' => asset('public/uploads/qrcodes/' . $printer->qrCode),
                'region' =>  $printer->region_detail != null ? $printer->region_detail->name : '',
                'customer' => $printer->customer_detail != null ? $printer->customer_detail->name : '',
                'location' => $printer->location_detail != null ? $printer->location_detail->name : '',
                'department' => $printer->department_detail != null ? $printer->department_detail->name : '',
                'brand' =>  $printer->brand_detail != null ? $printer->brand_detail->name : '',
                'model' => $printer->model_detail != null ?  $printer->model_detail->name : '',
                'user' => $printer->user_detail != null ? $printer->user_detail->first_name . ' ' . $printer->user_detail->last_name : '',
            ];
            $complaint = [
                'id' => $record->id,
                'complain_category' => $record->complain_category,
                'problem' => $record->problem,
                'screenshot' => $record->screenshot != '' ? asset('uploads/complaints/' . $record->screenshot) : null,
                'priority' => $record->priority,
                'description' => $record->description,
                'status' => $record->status,
                'created_at' => date('d-m-Y', strtotime($record->created_at)),
                'printer' => $data,
            ];

            return response()->json(['status' => 'success', 'meesage' => 'Records successfully found', 'data' => $complaint]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the complaint', 'error' => $e->getMessage()]);
        }
    }
}
