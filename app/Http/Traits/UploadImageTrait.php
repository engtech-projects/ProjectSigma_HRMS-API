<?php

namespace App\Http\Traits;

use App\Http\Requests\UploadImageRequest;
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
            $profilePhoto = $this->getExistingImage($employee);
            $url = 'images/' . $path . '/' . $hashName . $filename;
            if ($profilePhoto) {

                Storage::deleteDirectory('public/' . $profilePhoto->url);
            }
            Storage::disk('public')->put($url, $file);
            return $url;
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
            return $url;
        }
    }

    public function getExistingImage($employee)
    {
        $parentable_type = get_class($employee);
        return Image::where('parentable_id', $employee->id)
            ->where('image_type', 'profile_image')
            ->where('parentable_type', $parentable_type)
            ->first();
    }
}
