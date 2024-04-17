<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Employee;
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
        Log::info("Request In uploadProfileImage");
        Log::info($request);
        $profileImage = $this->uploadImage($request, 'profile_picture',$employee);
        Log::info($profileImage);
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
        $employee = Employee::findOrFail($id);
        $parentable_type = get_class($employee);
        $file = $this->uploadImage($request, 'digital_signature', $employee);
        $url = 'images/digital_signature/' . $file->hashName() . '/' . $file->getClientOriginalName();
        if ($file) {
            $profilePhoto = Image::where('parentable_id', $employee->id)
                ->where('image_type', 'signature')
                ->where('parentable_type', $parentable_type)
                ->first();
            if ($profilePhoto) {
                $profilePhoto->update([
                    'url' => $url,
                    'image_type' => "signature",
                    'parentable_id' => $employee->id,
                    'parentable_type' => Employee::class
                ]);
            } else {
                Image::create([
                    'url' => $url,
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
