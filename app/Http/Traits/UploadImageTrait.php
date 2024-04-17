<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($request, $path)
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $hashName = $file->hashName();
            $filename = $file->getClientOriginalName();
            $file->storePubliclyAs('images/' . $path . '/' . $hashName, $filename, 'public');
            return $file;
        }
    }
}
