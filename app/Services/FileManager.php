<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileManager
{
    //upload product image
    public function uploadFile($file)
    {
        $imageName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        // Ensure the image name is unique
        while (Storage::disk('public')->exists('uploads/' . $imageName)) {
            $imageName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        }
        Storage::putFileAs('public/uploads', $file, $imageName, [
            'visibility' => 'public',
            'directory_visibility' => 'public'
        ]);

        return 'uploads/' . $imageName;
    }
}
