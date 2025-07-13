<?php

namespace App\Http\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService {
    /**
     * Create a new class instance.
     */
    public function __construct() {
        //
    }

    public function storeImage(
        UploadedFile $file,
        string $filename,
        string $path,
        int $quality = 75,
        bool $crop = false
    ) {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);

        if ($crop) {
            $image->cover(512, 512);
        }

        $webp = $image->toWebp($quality);

        // if no use storage:link
        $fullPath = public_path($path);
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
        file_put_contents($fullPath . $filename, $webp);

        // if use storage:link
        // Storage::disk('public')->put($path.$filename, $webp);
    }

    public function deleteImage(string $url) {
        $relativePath = Str::after($url, config('app.url') . '/');
        $oldPath = public_path($relativePath);

        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
}
