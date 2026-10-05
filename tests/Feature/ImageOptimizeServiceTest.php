<?php

namespace Tests\Feature;

use App\Services\ImageOptimizeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizeServiceTest extends TestCase
{
    public function test_reduces_webp_size_when_default_encoding_would_grow_file(): void
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            $this->markTestSkipped('GD WebP support is required to generate the test image.');
        }

        $image = imagecreatetruecolor(512, 512);

        for ($y = 0; $y < 512; $y++) {
            for ($x = 0; $x < 512; $x++) {
                $color = ((($x * 3 + $y) % 256) << 16)
                    | ((($y * 5 + $x) % 256) << 8)
                    | (($x * $y) % 256);

                imagesetpixel($image, $x, $y, $color);
            }
        }

        ob_start();
        $sourceEncoded = imagewebp($image, null, 50);
        $original = ob_get_clean();
        imagedestroy($image);

        $this->assertTrue($sourceEncoded);
        $this->assertIsString($original);

        Storage::fake('public');

        $path = (new ImageOptimizeService())->optimize(
            UploadedFile::fake()->createWithContent('source.webp', $original)
        );

        $optimized = Storage::disk('public')->get($path);

        $this->assertLessThan(strlen($original), strlen($optimized));
    }
}
