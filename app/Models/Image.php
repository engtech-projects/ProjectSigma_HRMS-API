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
    use HasFactory, SoftDeletes;

    const PROFILE_IMAGE_TYPE = "profile-picture";
    const DIGITAL_SIGNATURE_TYPE = "signature";
    protected $fillable = [
        'url',
        'image_type',
        'parentable_id',
        'parentable_type',
    ];
    protected $appends = [
        'base64'
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
}
