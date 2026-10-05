<?php

namespace App\Filament\Resources\ImageOptimizes\Pages;

use App\Filament\Resources\ImageOptimizes\ImageOptimizeResource;
use App\Services\ImageOptimizeService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use RuntimeException;

class CreateImageOptimize extends CreateRecord
{
    protected static string $resource = ImageOptimizeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $file = $data['path'] ?? null;

        if (! $file instanceof TemporaryUploadedFile) {
            throw new RuntimeException('Please upload an image.');
        }

        $path = app(ImageOptimizeService::class)->optimize($file);

        $data['path'] = $path;
        $data['size'] = (string) Storage::disk('public')->size($path);
        $data['type'] = $file->getMimeType();

        return $data;
    }

    protected function getRedirectUrl(): string { return ImageOptimizeResource::getUrl('index'); }
}
