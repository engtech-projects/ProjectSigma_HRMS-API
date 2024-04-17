<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\UploadImageTrait;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    use UploadImageTrait;

    public function uploadProfileImage(Request $request, $id)
    {
        $employee_id = Employee::findOrFail($id);
        $profileImage = $this->uploadImage($request, 'profile_picture');
        $url = 'images/digital_signature/' . $profileImage->hashName() . '/' . $profileImage->getClientOriginalName();
        if ($profileImage) {
            Image::create([
                'url' => $url,
                'image_type' => "profile_image",
                'parentable_id' => $employee_id->id,
                'parentable_type' => Employee::class
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Profile picture successfully uploaded.',
        ]);
    }
    public function uploadDigitalSignature(Request $request, $id)
    {
        $employee_id = Employee::findOrFail($id);
        $file = $this->uploadImage($request, 'digital_signature');
        $url = 'images/digital_signature/' . $file->hashName() . '/' . $file->getClientOriginalName();
        if ($file) {
            Image::create([
                'url' => $url,
                'image_type' => "signature",
                'parentable_id' => $employee_id->id,
                'parentable_type' => Employee::class
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Digital signature successfully uploaded.',
        ]);
    }
}
