<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function handleFileUpload($request, $field, $obj, $uploadPath)
    {
        if ($request->hasFile($field)) {
            $image = $request->file($field);

            if ($image !== null) {
                $uniqueFileName = uniqid();
                $fileExtension = strtolower($image->getClientOriginalExtension());
                $newFileName = $uniqueFileName . '.' . $fileExtension;
                $path = public_path($uploadPath);

                if ($obj !== null) {
                    $oldImage = $obj->{$field};

                    if ($oldImage !== null && $oldImage !== "") {
                        $oldImagePath = $path . $oldImage;

                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                }

                $image->move($path, $newFileName);

                return $newFileName;
            }
        }

        return null;
    }

    public static function uploadImageInWEBP($request, $field, $obj, $uploadPath)
    {
        if ($request->hasFile($field)) {
            $image = $request->file($field);

            if ($image !== null) {
                $uniqueFileName = uniqid();
                $path = public_path($uploadPath);
                $newFileName = $uniqueFileName . '.webp';
                $webp = $path . '/' . $newFileName;
                $im = imagecreatefromstring(file_get_contents($image));
                imagepalettetotruecolor($im);
                $new_webp = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $webp);
                
                if ($obj !== null) {
                    $oldImage = $obj->{$field};

                    if ($oldImage !== null && $oldImage !== "") {
                        $oldImagePath = $path . $oldImage;

                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                }

                imagewebp($im, $new_webp, 80);

                return $newFileName;
            }
        }

        return null;
    }
}
