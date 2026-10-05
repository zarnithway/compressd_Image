<?php

namespace App\Filament\Resources\ImageOptimizes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ImageOptimizeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('path')
            ->label('Upload Image')
            ->image()
            ->required()
            ->storeFiles(false)
            ->maxSize(10240)
            ->acceptedFileTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
            ])
            ->helperText('Upload JPEG, PNG, or WebP.'),
        ]);
    }
}
