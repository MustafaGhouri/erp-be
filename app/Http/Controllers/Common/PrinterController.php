<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Printer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PrinterController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;

            // Validate the request to ensure it contains a CSV file
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'serial_number' => 'required',
                'brand' => 'required',
                'model' => 'required',
                'region' => 'required',
                'location' => 'required',
                'department' => 'required',
                'customer' => 'required',
                'counter' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'warning', 'message' => $validator->errors()]);
            }


            $printer =  Printer::create([
                'name' => $request->name,
                'serial_number' => $request->serial_number,
                'brand' => $request->brand,
                'model' => $request->model,
                'region' => $request->region,
                'location' => $request->location,
                'department' => $request->department,
                'customer' => $request->customer,
                'counter' => $request->counter,
                'user_id' => $user_id,
            ]);

            if ($printer) {
                $qrCodeGenerated = $this->generateQRCode($printer->id);

                if ($qrCodeGenerated) {
                    return $qrCodeGenerated;
                }
                return response()->json(['status' => "success", "message" =>  'Printer successfully stored']);
            }
        } catch (\Throwable $th) {
        }
    }

    public function generateQRCode($id)
    {
        try {
            // Generate a unique filename for the QR code image
            $filename = 'GCS-PRINTER' . time() . '.png';

            // Define the directory path to save the QR code
            $directory = asset('public/uploads/printers/qrcodes');
            $logo = asset('public/uploads/website/logo.png');

            $image = QrCode::format('png')
                ->merge($logo, .4, true)
                ->size(200)->errorCorrection('H')
                ->generate($id);

            Storage::disk('local')->put($filename, $image);

            // // Save the QR code image to the directory
            file_put_contents($directory . '/' . $filename, $image);

            // Find the product by its ID
            $Printer = Printer::find($id);

            if ($Printer) {
                // Update the Printer with the QR code path in the database
                $Printer->update([
                    'qrCode' => $filename
                ]);

                return response()->json(['status' => "success", "message" =>  'Printer stored successfully', 'data' => $Printer]);
            } else {
                return response()->json(['status' => "warning", "message" =>  'Printer not found']);
            }
        } catch (\Exception $th) {
            return response()->json(['status' => "warning", "message" =>   $th->getMessage()]);
        }
    }
}