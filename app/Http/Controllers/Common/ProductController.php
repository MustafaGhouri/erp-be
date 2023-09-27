<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $validator = Validator::make($request->all(), [
                "title" => 'required|min:4|unique:products',
                "slug" => 'required|min:4',
                "category" => 'required',
                "unit" => 'required',
                "alert_qty" => 'required',
            ], [
                "title.unique" => "The product name has already been taken.",
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", "message" => $validator->errors()]);
            }

            $product = Product::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'category' => $request->category,
                'unit' => $request->unit,
                'alert_qty' => $request->alert_qty,
                'user_id' => $user_id
            ]);

            if ($product) {
                $qrCodeGenerated = $this->generateQRCode($product->id, $product->slug);

                if ($qrCodeGenerated) {
                    return $qrCodeGenerated;
                }
                return response()->json(['status' => "success", "message" =>  'Product successfully stored']);
            }
            // if ($product) {
            //     return response()->json(['status' => "success", "message" =>  'Product successfully stored']);
            // }
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Something went wrong while storing the data', 'error' => $e->getMessage()]);
        }
    }

    public function generateQRCode($id, $slug)
    {
        try {
            // Generate a unique filename for the QR code image
            $filename = $slug . '.png';

            // Define the directory path to save the QR code
            $directory = public_path('uploads/qrcodes');
            $logo = public_path('uploads/website/logo.png');
            // $qrCode = QrCode::size(200)
            //     ->errorCorrection('M')
            //     ->generate($id);
            // Merge the QR code with the logo

            // $qrCode = QrCode::size(200)
            //     ->format('png')
            //     // ->merge('/public/uploads/website/logo.png')
            //     ->errorCorrection('M')
            //     ->generate(
            //         $id
            //     );

            $image = QrCode::format('png')
                ->merge($logo, 0.1, true)
                ->size(200)->errorCorrection('H')
                ->generate($id);

            Storage::disk('local')->put($filename, $image);



            // // Save the QR code image to the directory
            file_put_contents($directory . '/' . $filename, $image);

            // Find the product by its ID
            $product = Product::find($id);

            if ($product) {
                // Update the product with the QR code path in the database
                $product->update([
                    'qrcode' => $filename
                ]);

                return response()->json(['status' => "success", "message" =>  'QR Code generated']);
            } else {
                return response()->json(['status' => "warning", "message" =>  'Product not found']);
            }
        } catch (\Exception $th) {
            return response()->json(['status' => "warning", "message" =>   $th->getMessage()]);
        }
    }


    public function list()
    {
        try {
            $product = Product::orderBy('id', 'DESC')->get();

            return response()->json(['status' => "success", "message" =>  'Successfully data found', 'data' => $product]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Someting wrong while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
