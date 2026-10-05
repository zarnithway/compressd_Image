<?php

namespace App\Filament\Resources\ImageOptimizes\Pages;

use App\Filament\Resources\ImageOptimizes\ImageOptimizeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListImageOptimizes extends ListRecords
{
    protected static string $resource = ImageOptimizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
