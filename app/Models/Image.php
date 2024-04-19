<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function parentable()
    {
        return $this->morphTo();
    }
}
