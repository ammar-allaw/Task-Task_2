<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileOperationsTrait
{
    public function uploadFile($file, $directory,$disk='public')
    {
        $path = $file->store($directory,$disk);
        return $path;
    }

    public function deleteFile($path)
    {
        Storage::disk('public')->delete($path);
    }

    public function generateCustomName($file)
    {
        // Implement your custom name strategy here
        $extension = $file->getClientOriginalExtension();
        $fileName = uniqid() . '.' . $extension;
        return $fileName;
    }
}