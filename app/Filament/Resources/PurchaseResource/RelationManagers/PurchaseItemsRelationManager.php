<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseItems';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ref')
            ->columns([
                Tables\Columns\TextColumn::make('product.sku')
                    ->label('SKU'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Item Name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->formatNaira()
                    ->label('Unit Price'),
                Tables\Columns\TextColumn::make('total_price')
                    ->formatNaira()
                    ->label('Total Price'),
            ]);
    }
}
