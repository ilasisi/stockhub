<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Product;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Items')
                        ->schema([
                            Forms\Components\Repeater::make('purchaseItems')
                                ->label('')
                                ->relationship('purchaseItems')
                                ->schema([
                                    Forms\Components\Select::make('product_id')
                                        ->label('Item')
                                        ->options(Product::all()->pluck('name', 'id'))
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                            $product = Product::find($state);

                                            $set('available_qty', $product?->available_quantity);
                                            $set('quantity', 1);
                                            $set('unit_price', $product?->price);
                                            $set('total_price', $product?->price);

                                            $itemsTotal = collect($get('../../purchaseItems'))->sum('total_price');

                                            $set('../../items_total', $itemsTotal);
                                            $set('../../grand_total', $itemsTotal);
                                        })
                                        ->searchable()
                                        ->live()
                                        ->required()
                                        ->columnSpan(['lg' => 2]),
                                    Forms\Components\TextInput::make('quantity')
                                        ->required()
                                        ->live()
                                        ->numeric()
                                        ->label('Qty.')
                                        ->disabled(fn (Forms\Get $get) => ! $get('product_id'))
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                            $product = Product::find($get('product_id'));

                                            $set('total_price', $state * $product?->price ?? 0);

                                            $itemsTotal = collect($get('../../purchaseItems'))->sum('total_price');

                                            $set('../../items_total', $itemsTotal);
                                            $set('../../grand_total', $itemsTotal);
                                        }),
                                    Forms\Components\TextInput::make('available_qty')
                                        ->label('Available Qty.')
                                        ->dehydrated()
                                        ->disabled(),
                                    Forms\Components\TextInput::make('unit_price')
                                        ->label('Unit Price')
                                        ->numeric()
                                        ->prefix('₦')
                                        ->disabled()
                                        ->dehydrated(),
                                    Forms\Components\TextInput::make('total_price')
                                        ->label('Total Price')
                                        ->prefix('₦')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated(),
                                ])
                                ->collapsible()
                                ->cloneable()
                                ->addActionLabel('Add Item')
                                ->columns(['lg' => 6])
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                    $data['ref'] = str(str()->random(12))->upper();

                                    return $data;
                                })
                                ->itemLabel(fn (array $state): ?string => Product::find($state['product_id'])?->name ?? null),

                            Forms\Components\Group::make()
                                ->columns(['lg' => 4])
                                ->schema([
                                    Forms\Components\TextInput::make('items_total')
                                        ->label('Items Total')
                                        ->required()
                                        ->prefix('₦')
                                        ->disabled()
                                        ->dehydrated()
                                        ->numeric(),
                                ]),
                        ]),
                    Forms\Components\Wizard\Step::make('Details')
                        ->schema([
                            Forms\Components\Group::make()
                                ->columns(['lg' => 2])
                                ->schema([
                                    Forms\Components\Select::make('customer_id')
                                        ->relationship('customer', 'name')
                                        ->label('Select or Add Customer')
                                        ->required()
                                        ->preload()
                                        ->searchable()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required(),
                                            Forms\Components\TextInput::make('phone_number')
                                                ->tel(),
                                            Forms\Components\TextInput::make('email')
                                                ->email(),
                                        ]),
                                    Forms\Components\Select::make('payment_type_id')
                                        ->relationship('paymentType', 'name')
                                        ->label('Select or Add Payment Type')
                                        ->required()
                                        ->preload()
                                        ->searchable()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required(),
                                        ]),
                                    Forms\Components\Section::make('Discount/VAT')
                                        ->collapsible()
                                        ->collapsed()
                                        ->columns(['lg' => 2])
                                        ->schema([
                                            Forms\Components\Group::make()
                                                ->schema([
                                                    Forms\Components\Toggle::make('is_discount_amount')
                                                        ->label('Is Amount?')
                                                        ->live()
                                                        ->default(true)
                                                        ->onIcon('heroicon-m-currency-dollar')
                                                        ->offIcon('heroicon-m-receipt-percent')
                                                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', $get('items_total'));
                                                            $set('discount_amount', null);
                                                            $set('discount_percentage', null);
                                                        }),
                                                    Forms\Components\TextInput::make('discount_amount')
                                                        ->label('Discount (₦)')
                                                        ->live(onBlur: true)
                                                        ->minValue(0)
                                                        ->prefix('₦')
                                                        ->numeric()
                                                        ->hidden(fn (Forms\Get $get): bool => ! $get('is_discount_amount'))
                                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', $get('items_total') - $state + $get('vat_amount'));
                                                        }),
                                                    Forms\Components\TextInput::make('discount_percentage')
                                                        ->label('Discount (%)')
                                                        ->live(onBlur: true)
                                                        ->minValue(0)
                                                        ->numeric()
                                                        ->hidden(fn (Forms\Get $get): bool => $get('is_discount_amount'))
                                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', round($get('items_total') - ($state / 100 * $get('items_total')), 2));
                                                        }),
                                                ]),
                                            Forms\Components\Group::make()
                                                ->schema([
                                                    Forms\Components\Toggle::make('is_vat_amount')
                                                        ->label('Is Amount?')
                                                        ->live()
                                                        ->default(true)
                                                        ->onIcon('heroicon-m-currency-dollar')
                                                        ->offIcon('heroicon-m-receipt-percent')
                                                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', $get('items_total'));
                                                            $set('vat_amount', null);
                                                            $set('vat_percentage', null);
                                                        }),
                                                    Forms\Components\TextInput::make('vat_amount')
                                                        ->label('VAT (₦)')
                                                        ->minValue(0)
                                                        ->live(onBlur: true)
                                                        ->hidden(fn (Forms\Get $get): bool => ! $get('is_vat_amount'))
                                                        ->prefix('₦')
                                                        ->numeric()
                                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', $get('items_total') + $state - $get('discount_amount'));
                                                        }),
                                                    Forms\Components\TextInput::make('vat_percentage')
                                                        ->label('VAT (%)')
                                                        ->minValue(0)
                                                        ->live(onBlur: true)
                                                        ->hidden(fn (Forms\Get $get) => $get('is_vat_amount'))
                                                        ->numeric()
                                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                                            $set('grand_total', round($get('items_total') + ($state / 100 * $get('items_total')), 2));
                                                        }),
                                                ]),
                                        ]),
                                    Forms\Components\Section::make()
                                        ->columns(['lg' => 4])
                                        ->schema([
                                            Forms\Components\TextInput::make('amount_tender')
                                                ->label('Amount Tender')
                                                ->required()
                                                ->live()
                                                ->minValue(0)
                                                ->prefix('₦')
                                                ->numeric()
                                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get): void {
                                                    $set('change_due', $state - $get('grand_total'));
                                                }),
                                            Forms\Components\TextInput::make('change_due')
                                                ->label('Change Due')
                                                ->required()
                                                ->prefix('₦')
                                                ->disabled()
                                                ->dehydrated()
                                                ->numeric(),
                                            Forms\Components\TextInput::make('items_total')
                                                ->label('Items Total')
                                                ->required()
                                                ->prefix('₦')
                                                ->disabled()
                                                ->dehydrated()
                                                ->numeric(),
                                            Forms\Components\TextInput::make('grand_total')
                                                ->label('Grand Total')
                                                ->required()
                                                ->prefix('₦')
                                                ->disabled()
                                                ->dehydrated()
                                                ->numeric(),
                                        ]),
                                ]),
                        ]),
                ])->columnSpanFull(),
            ]);
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
                Tables\Columns\TextColumn::make('items_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat_percentage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_tender')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('change_due')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchases::route('/'),
            'create' => Pages\CreatePurchase::route('/create'),
        ];
    }
}
