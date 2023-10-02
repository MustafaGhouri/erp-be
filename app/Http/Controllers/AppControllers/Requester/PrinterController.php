<?php

namespace App\Http\Controllers\AppControllers\Requester;

use App\Http\Controllers\Controller;
use App\Models\Printer;
use Illuminate\Http\Request;

class PrinterController extends Controller
{
    public function show($id)
    {
        try {

            $printer = Printer::with(['region_detail', 'customer_detail', 'location_detail', 'department_detail', 'brand_detail', 'model_detail', 'user_detail'])->where('id', $id)->orderBy('id', 'desc')->first();
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

            return response()->json(['status' => 'success', 'message' => 'Printer successfully retrieved', 'data' => $data]);
        } catch (\Exception $th) {
            return response()->json(['status' => "warning", "message" => $th->getMessage()]);
        }
    }
}
