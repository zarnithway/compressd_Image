<?php

namespace App\Filament\Resources\ImageOptimizes;

use App\Filament\Resources\ImageOptimizes\Pages\CreateImageOptimize;
use App\Filament\Resources\ImageOptimizes\Pages\EditImageOptimize;
use App\Filament\Resources\ImageOptimizes\Pages\ListImageOptimizes;
use App\Filament\Resources\ImageOptimizes\Pages\ViewImageOptimize;
use App\Filament\Resources\ImageOptimizes\Schemas\ImageOptimizeForm;
use App\Filament\Resources\ImageOptimizes\Schemas\ImageOptimizeInfolist;
use App\Filament\Resources\ImageOptimizes\Tables\ImageOptimizesTable;
use App\Models\ImageOptimize;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ImageOptimizeResource extends Resource
{
    protected static ?string $model = ImageOptimize::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'ImageOptimize';

    public static function form(Schema $schema): Schema
    {
        return ImageOptimizeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ImageOptimizeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ImageOptimizesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImageOptimizes::route('/'),
            'create' => CreateImageOptimize::route('/create'),
            'view' => ViewImageOptimize::route('/{record}'),
            'edit' => EditImageOptimize::route('/{record}/edit'),
        ];
    }
}
