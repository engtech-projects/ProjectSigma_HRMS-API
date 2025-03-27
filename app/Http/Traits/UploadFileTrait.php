<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

trait UploadFileTrait
{
    public function uploadFile($file, $fileLocation, $newName = null)
    {
        $hashmake = Hash::make('secret');
        $hashname = substr(hash('sha256', $hashmake), 0, 20);
        $originalName = $newName ?? $file->getClientOriginalName();
        $file->storePubliclyAs($fileLocation . $originalName, 'public');
        return $fileLocation . $hashname . "/" . $originalName;
    }

    public function uploadFileStoragedisk($file, $fileLocation, $filename)
    {
        $hashmake = Hash::make('secret');
        $hashname = substr(hash('sha256', $hashmake), 0, 20);
        $outputFile = $fileLocation . $hashname . "/" . $filename;
        Storage::disk('public')->put($outputFile, $file);
        return $outputFile;
    }

    public function replaceUploadFile($oldFile, $file, $fileLocation)
    {
        $oldfileUniqueFolder = explode("/", $oldFile);
        array_pop($oldfileUniqueFolder);
        Storage::deleteDirectory("public/" . implode("/", $oldfileUniqueFolder)); // DELETE OLD FILE
        $hashmake = Hash::make('secret');
        $hashname = substr(hash('sha256', $hashmake), 0, 20);
        $originalName = $file->getClientOriginalName();
        $file->storePubliclyAs($fileLocation . $hashname, $originalName, 'public');
        return $fileLocation . $hashname . "/" . $originalName;
    }
}
