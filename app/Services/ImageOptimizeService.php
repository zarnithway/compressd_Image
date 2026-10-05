<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageOptimizeService
{
    public function optimize(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (! isset($extensions[$mime])) {
            throw new RuntimeException('Unsupported image format.');
        }

        if (extension_loaded('gd')) {
            $driver = new GdDriver();
        } elseif (extension_loaded('imagick')) {
            $driver = new ImagickDriver();
        } else {
            throw new RuntimeException(
                'Enable GD or Imagick before uploading images.'
            );
        }

        $manager = new ImageManager($driver);

        $original = file_get_contents($file->getRealPath());

        if ($original === false) {
            throw new RuntimeException('Could not read uploaded image.');
        }

        $image = $manager->decode($original);

        $encoder = match ($mime) {
            'image/jpeg' => new JpegEncoder(quality: 80),
            'image/png' => new PngEncoder(),
            'image/webp' => new WebpEncoder(quality: 75),
        };

        $encoded = (string) $image->encode($encoder);

        // Keep the original if encoding makes the image larger.
        $contents = strlen($encoded) < strlen($original)
            ? $encoded
            : $original;

        $path = 'optimized/' . Str::uuid()
            . '.' . $extensions[$mime];

        if (! Storage::disk('public')->put($path, $contents)) {
            throw new RuntimeException('Failed to store optimized image.');
        }

        return $path;
    }
}
