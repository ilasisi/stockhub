<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Filament\Resources\PurchaseResource\RelationManagers\PurchaseItemsRelationManager;
use App\Models\Purchase;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                static::getItemsTotalField(),
            ]);
    }

    public static function getItemsTotalField(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('items_total')
            ->label('Items Total')
            ->required()
            ->prefix('₦')
            ->disabled()
            ->dehydrated()
            ->numeric();
    }

    public static function table(Table $table): Table
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
                        self::js('window.print();');
                    })
                    ->modalSubmitActionLabel('Print')
                    ->stickyModalFooter()
                    ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                    ->closeModalByClickingAway(false),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Purchase Details')
                    ->schema([
                        Components\Split::make([
                            Components\Grid::make()
                                ->columns(['lg' => 3])
                                ->schema([
                                    Components\Group::make([
                                        Components\TextEntry::make('ref')
                                            ->label('#Reference ID')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large)
                                            ->copyable()
                                            ->copyMessage('Batch ID Copied!')
                                            ->copyMessageDuration(1500),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('customer.name')
                                            ->label('Customer Name')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('user.name')
                                            ->label('Sold By')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('paymentType.name')
                                            ->label('Payment Type')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('discount')
                                            ->label('Discount Amount')
                                            ->formatStateUsing(fn (Model $record) => $record->discount)
                                            ->default(0)
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('vat')
                                            ->label('VAT')
                                            ->formatStateUsing(fn (Model $record) => $record->vat)
                                            ->default(0)
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('amount_tender')
                                            ->label('Amount Tender')
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('change_due')
                                            ->label('Change Due')
                                            ->default(0)
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('items_total')
                                            ->label('Items Total Amount')
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('grand_total')
                                            ->label('Grand Total')
                                            ->formatNaira()
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('status')
                                            ->capitalize()
                                            ->badge()
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success'),
                                    ]),
                                    Components\Group::make([
                                        Components\TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->dateTime('M d, Y h:mA')
                                            ->weight(FontWeight::Bold)
                                            ->size(TextEntrySize::Large),
                                    ]),
                                ]),
                        ])->from('lg'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PurchaseItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
            'view' => Pages\ViewPurchase::route('/{record}'),
        ];
    }
}
