<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductResource::getEloquentQuery())
            ->defaultPaginationPageOption(6)
            ->defaultSort('available_quantity', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price')
                    ->formatNaira(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Avail. Qty.')
                    ->badge()
                    ->color(fn (int $state) => $state <= 20 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record])),
            ], position: ActionsPosition::BeforeColumns)
            ->deferLoading()
            ->striped();
    }
}
