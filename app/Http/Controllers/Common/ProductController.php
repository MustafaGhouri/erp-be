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
                "serial_number" => 'required',
                "model_number" => 'required',
                "brand" => 'required',
                "category" => 'required',
                "unit" => 'required',
                "alert_qty" => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => "warning", 'message' => $validator->errors()]);
            }

            $product = Product::create([
                'title' => $request->title,
                'serial_number' => $request->serial_number,
                'model_number' => $request->model_number,
                'brand' => $request->brand,
                'slug' => $request->slug,
                'category' => $request->category,
                'unit' => $request->unit,
                'alert_qty' => $request->alert_qty,
                'user_id' => $user_id
            ]);

            // if ($product) {
            //     $qrCodeGenerated = $this->generateQRCode($product->id, $product->slug);

            //     if ($qrCodeGenerated) {
            //         return $qrCodeGenerated;
            //     }
            //     return response()->json(['status' => "success", "message" =>  'Product successfully stored']);
            // }

            if ($product) {
                return response()->json(['status' => "success", "message" =>  'Product successfully stored']);
            }
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

            $image = QrCode::format('png')
                ->merge($logo, .4, true)
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
            $data = [];
            $products = Product::with(['user', 'category', 'unit', 'brand'])->orderBy('id', 'DESC')->get();

            foreach ($products as $product) {
                // Check if the relationships exist and are valid before accessing their properties
                $unitData = null;
                // if ($product->unit) {
                //     $unitData = [
                //         'id' => $product->unit->id,
                //         'name' => $product->unit->name,
                //     ];
                // }

                $categoryData = null;
                // if ($product->category) {
                //     $categoryData = [
                //         'id' => $product->category->id,
                //         'name' => $product->category->name,
                //     ];
                // }

                $brandData = null;
                // if ($product->brand) {
                //     $brandData = [
                //         'id' => $product->brand->id,
                //         'name' => $product->brand->name,
                //     ];
                // }

                $userData = null;
                if ($product->user) {
                    $userData = [
                        'id' => $product->user->id,
                        'first_name' => $product->user->first_name,
                        'last_name' => $product->user->last_name,
                        'email' => $product->user->email,
                    ];
                }

                array_push($data, [
                    'id' => $product->id,
                    'title' => $product->title,
                    'serial_number' => $product->serial_number,
                    'model_number' => $product->model_number,
                    'slug' => $product->slug,
                    'alert_qty' => $product->alert_qty,
                    'quantity' => $product->quantity,
                    'qrcode' => asset('uploads/products/qrcodes/', $product->qrcode),
                    'created_at' => $product->created_at,
                    'unit' => $unitData,
                    'category' => $categoryData,
                    'brand' => $brandData,
                    'user' => $userData,
                ]);
            }

            return response()->json(['status' => "success", "message" =>  'Successfully data found', 'data' => $products]);
        } catch (\Exception $e) {
            return response()->json(['status' => "error", "message" =>  'Someting wrong while retrieving the data', 'error' => $e->getMessage()]);
        }
    }
}
