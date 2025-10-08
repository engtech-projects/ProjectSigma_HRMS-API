<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\UploadImageTrait;
use App\Http\Requests\UploadImageRequest;

class ImageController extends Controller
{
    use UploadImageTrait;

    public function uploadProfileImage(UploadImageRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $profileImage = $this->uploadImage($request, 'profile_picture', $employee);
        if ($profileImage) {
            $profilePhoto = $this->getExistingImage($employee, $request->image_type);
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
    public function uploadDigitalSignature(UploadImageRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $file = $this->uploadImage($request, 'digital_signature', $employee, 'signature');
        if ($file) {
            $signaturePhoto = $this->getExistingImage($employee, $request->image_type);
            if ($signaturePhoto) {
                $signaturePhoto->update([
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
