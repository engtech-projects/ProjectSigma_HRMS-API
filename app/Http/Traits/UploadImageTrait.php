<?php

namespace App\Http\Traits;

use App\Models\Image;
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
            $file->storePubliclyAs('images/' . $path . '/' . $hashName, $filename, 'public');
            return $file;
        }
    }
}
