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
        $employee = Employee::findOrFail($id);
        $parentable_type = get_class($employee);
        $profileImage = $this->uploadImage($request, 'profile_picture', $employee);
        if ($profileImage) {
            $profilePhoto = Image::where('parentable_id', $employee->id)
                ->where('image_type', 'profile_image')
                ->where('parentable_type', $parentable_type)
                ->first();
            if ($profilePhoto) {
                $profilePhoto->update([
                    'url' => $profileImage,
                    'image_type' => "profile_image",
                    'parentable_id' => $employee->id,
                    'parentable_type' => Employee::class
                ]);
            } else {
                Image::create([
                    'url' => $profileImage,
                    'image_type' => "profile_image",
                    'parentable_id' => $employee->id,
                    'parentable_type' => Employee::class
                ]);
            }
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Profile picture successfully uploaded.',
        ]);
    }
    public function uploadDigitalSignature(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $parentable_type = get_class($employee);
        $file = $this->uploadImage($request, 'digital_signature', $employee);
        if ($file) {
            $signature = Image::where('parentable_id', $employee->id)
                ->where('image_type', 'signature')
                ->where('parentable_type', $parentable_type)
                ->first();
            if ($signature) {
                $signature->update([
                    'url' => $file,
                    'image_type' => "signature",
                    'parentable_id' => $employee->id,
                    'parentable_type' => Employee::class
                ]);
            } else {
                Image::create([
                    'url' => $file,
                    'image_type' => "signature",
                    'parentable_id' => $employee->id,
                    'parentable_type' => Employee::class
                ]);
            }
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Digital signature successfully uploaded.',
        ]);
    }
}
