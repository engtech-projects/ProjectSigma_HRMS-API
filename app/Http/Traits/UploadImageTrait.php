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
        } else if (gettype($request->input('image_file')) == "string") {
            list($mime, $data)   = explode(';', $request->input('image_file'));
            list(, $data)       = explode(',', $data);
            list(, $type)       = explode('/', $mime);
            $file = base64_decode($data);
            $hashmake = Hash::make('secret');
            $hashname = hash('sha256', $hashmake);
            $randName = mt_rand().time();
            return Storage::put('images/' . $path . '/' . $hashName . '/' . $randName . $type, $file)
        }
    }
}
