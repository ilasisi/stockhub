<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use Filament\Actions;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paymentType.name')
                    ->label('Paid With')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sold By')
                    ->default('N/A')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->numeric()
                    ->formatNaira()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->capitalize()
                    ->badge()
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print_invoice')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->modalContent(fn ($record): View => view(
                        'filament.pages.actions.print_invoice',
                        ['record' => $record],
                    ))
                    ->modalHeading('')
                    ->modalWidth('sm')
                    ->action(function (): void {
                        $this->js('window.print();');
                    })
                    ->modalSubmitActionLabel('Print')
                    ->stickyModalFooter()
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
