<?php

namespace App\Filament\Resources\ImageOptimizes\Pages;

use App\Filament\Resources\ImageOptimizes\ImageOptimizeResource;
use App\Models\ImageOptimize;
use App\Services\ImageOptimizeService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditImageOptimize extends EditRecord
{
    protected static string $resource = ImageOptimizeResource::class;

    protected ?string $oldImagePath = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->after(function (ImageOptimize $record): void {
                    $path = $record->path;

                    if (
                        $path && ! ImageOptimize::where('path', $path)->exists()
                    ) {
                        Storage::disk('public')->delete($path);
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $file = $data['path'] ?? null;

        if (! $file instanceof TemporaryUploadedFile) {
            $data['path'] = $this->record->path;

            return $data;
        }

        $this->oldImagePath = $this->record->path;

        $newPath = app(ImageOptimizeService::class)->optimize($file);

        $data['path'] = $newPath;
        $data['size'] = (string) Storage::disk('public')->size($newPath);
        $data['type'] = Storage::disk('public')->mimeType($newPath);

        return $data;
    }

    protected function afterSave(): void
    {
        $oldPath = $this->oldImagePath;

        if ( $oldPath && $oldPath !== $this->record->path && ! ImageOptimize::where('path', $oldPath)->exists() ) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
