<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\CounterLog;
use Illuminate\Http\Request;

class CounterLogsController extends Controller
{
    //

    public function list($id, $date)
    {
        try {
            $counterLogs = CounterLog::whereRaw("DATE_FORMAT(created_at, '%m-%Y') = ?", [$date])->with(['locationDetails', 'customerDetails', 'complaintDetails', 'userDetails'])->where('printer', $id)->orderBy('id', 'DESC')->get();
            $data = [];
            foreach ($counterLogs as $counterLog) {
                array_push($data, [
                    'id' => $counterLog->id,
                    'before_counter' => $counterLog->before_counter,
                    'counter' => $counterLog->counter,
                    'counter_file' => asset('uploads/counters/' . $counterLog->counter_file),
                    'log_type' => $counterLog->log_type,
                    'user' => [
                        'id' => $counterLog->userDetails->id,
                        'name' => $counterLog->userDetails->first_name . ' ' . $counterLog->userDetails->last_name,
                    ],
                    'complaint_problem' => $counterLog->complaintDetails != null ? $counterLog->complaintDetails->problem : '',
                    'complaint_id' => $counterLog->complaintDetails != null ? $counterLog->complaintDetails->id : '',
                    'customer' => $counterLog->customerDetails != null ? $counterLog->customerDetails->name : '',
                    'location' => $counterLog->locationDetails != null ? $counterLog->locationDetails->name : '',

                ]);
            }

            return response()->json(
                [
                    'status' => 'success',
                    'message' => 'Successfully data found',
                    'data' => $data
                ]
            );
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(
                [
                    'status' => 'warning',
                    'message' => 'Something wrong while retrieving the data',
                    'error' => $e->getMessage()
                ]
            );
        }
    }
}
