<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use stdClass;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Store';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->live()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->ownedByMyBranch()
                    )
                    ->preload()
                    ->live()
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set): void {
                        $category = Category::find($state);

                        if ('create' === $operation && $category) {
                            $set('sku', (string) str(str($category->name)->substr(0, 3) . '-' . str()->random(9))->upper());
                        }
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->unique(Category::class, 'name', ignoreRecord: false)
                            ->required(),
                        Forms\Components\Hidden::make('branch_id')
                            ->default(function () {
                                $tenant = Filament::getTenant();

                                return $tenant->id;
                            }),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->unique(Category::class, 'name', ignoreRecord: false)
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₦'),
                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->default(str(str()->random(12))->upper())
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->visibleOn('create')
                    ->unique(Product::class, 'sku', ignoreRecord: true),
                Forms\Components\TextInput::make('available_quantity')
                    ->label('Available Qty.')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(['lg' => 3]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('SN')
                    ->state(
                        static function (HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('sku')
                    ->uppercase()
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatNaira()
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_quantity')
                    ->label('Available Qty.')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state <= 10 ? 'danger' : ($state <= 25 ? 'warning' : 'success'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('items_sold')
                    ->label('Items Sold')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 10 ? 'success' : 'gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->fromTable()
                                ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d-m-h-s'))
                                ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                                ->ignoreFormatting(['price'])
                                ->except(['index']),
                        ]),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
