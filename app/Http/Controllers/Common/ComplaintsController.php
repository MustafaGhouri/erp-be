<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Printer;
use Illuminate\Http\Request;

class ComplaintsController extends Controller
{
    public function list_by_region($id, $date)
    {

        try {
            $data = [];
            $printer_data = [];
            $monthYear = $date;
            $records = Complaint::whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$monthYear])->where('region', $id)->orderBy('id', 'DESC')->get();
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
                    'counter' => $record->counter,
                    'counter_file' => $record->counter_file,
                    'complete_date' => $record->complete_date,
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

    public function list_by_printer($id, $date)
    {

        try {
            $data = [];
            $printer_data = [];
            $monthYear = $date;
            $records = Complaint::whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$monthYear])->where('printer', $id)->orderBy('id', 'DESC')->get();
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
                    'counter' => $record->counter,
                    'counter_file' => $record->counter_file != null ? asset('uploads/counters/' . $record->counter_file) : '',
                    'screenshot' => $record->screenshot != null ? asset('uploads/complaints/' . $record->screenshot) : '',
                    'complete_date' => $record->complete_date,
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
}
