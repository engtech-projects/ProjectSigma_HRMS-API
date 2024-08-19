<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const PROFILE_IMAGE_TYPE = "profile-picture";
    public const DIGITAL_SIGNATURE_TYPE = "signature";
    protected $fillable = [
        'url',
        'image_type',
        'parentable_id',
        'parentable_type',
    ];

    public function parentable()
    {
        return $this->morphTo();
    }

    protected function base64(): Attribute
    {
        if (Storage::disk("public")->exists($this->url)) {
            $mimeType = File::mimeType('storage/' . $this->url);
            return Attribute::make(
                get: fn () => "data:" . $mimeType . ";base64," . base64_encode(file_get_contents("storage/" . $this->url))
            );
        } else {
            return Attribute::make(
                get: fn () => "File doesn't exists."
            );
        }
    }


    protected function base64Compressed(): Attribute // Compressed Base64 String - for bulk
    {
        $width = 112;
        $height = 112;
        if (Storage::disk("public")->exists($this->url)) {
            $mimeType = File::mimeType('storage/' . $this->url);
            // Resizing Image
            $imageData = file_get_contents("storage/" . $this->url);
            $sourceImage = imagecreatefromstring($imageData);
            $originalWidth = imagesx($sourceImage);
            $originalHeight = imagesy($sourceImage);
            $aspectRatio = $originalWidth / $originalHeight;
            if ($originalWidth > $originalHeight) {
                $newWidth = $width;
                $newHeight = $width / $aspectRatio;
            } else {
                $newHeight = $height;
                $newWidth = $height * $aspectRatio;
            }
            $resizedImage = imagescale($sourceImage, $newWidth, $newHeight);
            ob_start();
            imagejpeg($resizedImage);
            $imageData = ob_get_contents();
            ob_end_clean();
            // End Resize image
            return Attribute::make(
                get: fn () => "data:" . $mimeType . ";base64," . base64_encode($imageData)
                // get: fn () => "data:" . $mimeType . ";base64," . base64_encode(file_get_contents("storage/" . $this->url)) // Original File Size
            );
        } else {
            return Attribute::make(
                get: fn () => "File doesn't exists."
            );
        }
    }
}
