<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($request, $path)
    {
        if ($request->hasfile('image_file')) {
            $file = $request->file('image_file');
            $hashName = $file->hashName();
            $filename = $file->getClientOriginalName();
            $file->storePubliclyAs('images/' . $path . '/' . $hashName, $filename, 'public');
            return $file;
        }
    }
}
