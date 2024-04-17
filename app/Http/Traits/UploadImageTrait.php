<?php

namespace App\Http\Traits;

trait UploadImageTrait
{
    public function uploadImage($request, $path)
    {
        if ($request->hasfile('image_file')) {
            $file = $request->file('image_file');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extenstion;
            $file->move('images/' . $path . '/', $filename);
            return $filename;
        }
    }
}
