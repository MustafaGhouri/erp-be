<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function list()
    {
        try {
            $data = [];

            $models = Region::orderBy('id', 'desc')->get();
            if (empty($models)) {
                return response()->json(['status' => 'warning', 'message' => 'Regions not found', 'data' => []]);
            }
 


            return response()->json(['status' => 'success', 'message' => 'Regions successfully retrieved', 'data' => $models]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'warning', 'message' => 'Something while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
