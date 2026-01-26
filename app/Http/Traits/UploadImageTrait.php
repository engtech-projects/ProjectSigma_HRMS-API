<?php

namespace App\Http\Traits;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

trait UploadImageTrait
{
    public function uploadImage($request, $path, $employee)
    {
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $url = 'images/' . $path;
            $this->deleteExistingDirectory($employee, $request->image_type);
            $fileUploaded = Storage::disk('public')->put($url, $file);
            return $fileUploaded;
        } elseif (gettype($request->input('image_file')) == "string") {
            $img_64 = $request->input('image_file');
            $extension = explode('/', explode(':', substr($img_64, 0, strpos($img_64, ';')))[1])[1];
            $replace = substr($img_64, 0, strpos($img_64, ',') + 1);
            $image = str_replace($replace, '', $img_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;
            $url = 'images/' . $path . '/' . $imageName;
            $this->deleteExistingDirectory($employee, $request->image_type);
            Storage::disk('public')->put($url, base64_decode($image));
            return $url;
        }
    }

    private function deleteExistingDirectory($employee, $type)
    {
        $file = $this->getExistingImage($employee, $type);
        if ($file) {
            Storage::delete('public/' . $file->url);
        }
    }

    public function getExistingImage($employee, $type)
    {
        $parentable_type = get_class($employee);
        return Image::where('parentable_id', $employee->id)
            ->where('image_type', $type)
            ->where('parentable_type', $parentable_type)
            ->first();
    }
}
