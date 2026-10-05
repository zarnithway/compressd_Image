<?php

namespace App\Filament\Resources\ImageOptimizes\Tables;

use App\Models\ImageOptimize;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ImageOptimizesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Image')
                    ->getStateUsing(
                        fn ($record) => asset('storage/' . $record->path)
                    )
                    ->imageHeight(64)
                    ->extraImgAttributes([
                        'style' => 'object-fit: contain;',
                    ]),
                TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(function ($state) {
                        $size = (float) $state;

                        if ($size >= 1024 * 1024) {
                            return number_format($size / (1024 * 1024), 2) . ' MB';
                        }

                        return number_format($size / 1024, 2) . ' KB';
                    }),
                TextColumn::make('type')
                    ->label('Type'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'image/webp' => 'WebP',
                    ])
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                ->after(function (ImageOptimize $record): void {
                    $path = $record->path;

                    if (
                        $path && ! ImageOptimize::where('path', $path)->exists()
                    ) {
                        Storage::disk('public')->delete($path);
                    }
                }),
                Action::make('viewImage')
                    ->label('View Image')
                    ->icon('heroicon-o-eye')
                    ->color('secondary')
                    ->modalHeading('Image Preview')
                    ->modalContent(fn ($record) => view(
                        'filament.actions.image-preview',
                        ['image' => asset('storage/' . $record->path)]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
