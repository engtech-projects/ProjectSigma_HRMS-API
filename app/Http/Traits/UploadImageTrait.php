<?php

namespace App\Http\Traits;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($request, $path, $employee)
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $hashName = $file->hashName();
            $filename = $file->getClientOriginalName();
            $parentable_type = get_class($employee);
            $profilePhoto = Image::where('parentable_id', $employee->id)
                ->where('image_type', 'signature')
                ->where('parentable_type', $parentable_type)
                ->first();
            if ($profilePhoto) {
                /* unlink(public_path('images/digital_signature/QNdQqUWz5V2YXyMx0NhTN4Zop3fHiPM26QLVVaMs.jpg/luffy.jpg')); */
            }
            $url = 'images/' . $path . '/' . $hashName . '/' . $filename;
            $file->storePubliclyAs('images/' . $path . '/' . $hashName, $filename, 'public');
            return Storage::url($url);
        } else if (gettype($request->input('image_file')) == "string") {
            $img_64 = $request->input('image_file');
            $extension = explode('/', explode(':', substr($img_64, 0, strpos($img_64, ';')))[1])[1];
            $replace = substr($img_64, 0, strpos($img_64, ',') + 1);
            $image = str_replace($replace, '', $img_64);
            $image = str_replace(' ', '+', $image);

            $imageName = Str::random(10) . '.' . $extension;
            $url = 'images/' . $path . '/' . $imageName . '/' . $imageName;
            $randName = mt_rand() . time();
            $url = 'images/' . $path . '/' . $randName . '/' . $imageName;
            Storage::disk('public')->put($url, base64_decode($image));
            return Storage::url($url);
            /* list($mime, $data)   = explode(';', $request->input('image_file'));
            list(, $data)       = explode(',', $data);
            list(, $type)       = explode('/', $mime);
            $file = base64_decode($data);
            $hashmake = Hash::make('secret');
            $hashname = hash('sha256', $hashmake);
            $randName = mt_rand() . time();
            Storage::put('images/' . $path . '/' . $hashname . '/' . $randName . $type, $file); */
        }
    }
}
