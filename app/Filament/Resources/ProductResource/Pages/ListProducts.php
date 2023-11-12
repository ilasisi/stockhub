<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Konnco\FilamentImport\Actions\ImportAction;
use Konnco\FilamentImport\Actions\ImportField;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ImportAction::make()
                ->color('success')
                ->uniqueField('name')
                ->fields([
                    ImportField::make('name')
                        ->required(),
                    ImportField::make('sku')
                        ->required(),
                    ImportField::make('category.name')
                        ->required()
                        ->label('Category'),
                    ImportField::make('price')
                        ->required(),
                    ImportField::make('available_quantity')
                        ->required(),
                    ImportField::make('description'),
                ], columns: 2)
                ->handleRecordCreation(function (array $data) {
                    if ($category = CategoryResource::getEloquentQuery()->where('name', $data['category']['name'])->first()) {
                        return Product::create(collect($data)->merge([
                            'category_id' => $category->id,
                        ]));
                    }

                    return new Product();
                }),
            ExportAction::make()
                ->color('info')
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d-m-h-s'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                        ->ignoreFormatting(['price'])
                        ->except(['index']),
                ]),
        ];
    }
}
