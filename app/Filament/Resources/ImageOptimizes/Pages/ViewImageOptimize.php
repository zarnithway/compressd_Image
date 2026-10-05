<?php

namespace App\Filament\Resources\ImageOptimizes\Pages;

use App\Filament\Resources\ImageOptimizes\ImageOptimizeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewImageOptimize extends ViewRecord
{
    protected static string $resource = ImageOptimizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
