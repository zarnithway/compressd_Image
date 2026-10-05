<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use RuntimeException;

class ImageOptimizeService
{

    private const QUALITY_LEVELS = [80, 75, 70, 65, 60, 55,];

    public function optimize(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        $supportedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (! in_array($mime, $supportedTypes, true)) {
            throw new RuntimeException(
                'Unsupported image format.'
            );
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

        $original = file_get_contents(
            $file->getRealPath()
        );

        if ($original === false) {
            throw new RuntimeException(
                'Could not read uploaded image.'
            );
        }

        $originalSize = strlen($original);

        try {
            $image = $manager->decode($original);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Invalid image file.'
            );
        }

        $bestEncoded = null;
        $bestSize = PHP_INT_MAX;

        foreach (self::QUALITY_LEVELS as $quality) {
            $encoded = (string) $image->encode(new WebpEncoder(quality: $quality));

            $size = strlen($encoded);

            if ($size < $bestSize) {
                $bestEncoded = $encoded;
                $bestSize = $size;
            }

            $reduction = $originalSize > 0
                ? (($originalSize - $size) / $originalSize) * 100
                : 0;

            if ($reduction >= 25) {
                break;
            }
        }

        if ($bestEncoded === null) {
            throw new RuntimeException(
                'Failed to optimize image.'
            );
        }

        $path = 'optimized/' .
            Str::uuid() .
            '.webp';

        if (! Storage::disk('public')->put(
            $path,
            $bestEncoded
        )) {
            throw new RuntimeException(
                'Failed to store optimized image.'
            );
        }

        return $path;
    }
}
