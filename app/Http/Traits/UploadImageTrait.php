<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($request, $path)
    {
        Log::info("Inside UploadImage Trait");
        Log::info($request);
        if ($request->hasFile('image_file')) {
            Log::info("Inside UploadImage Trait - true fasfile image_file");
            $file = $request->file('image_file');
            Log::info($file);
            $hashName = $file->hashName();
            $filename = $file->getClientOriginalName();
            $file->storePubliclyAs('images/' . $path . '/' . $hashName, $filename, 'public');
            return $file;
        }
    }
}
