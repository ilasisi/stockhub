<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Purchase;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $products = Product::ownedByMyBranch();

        $totalSales = '₦' . number_format(Purchase::ownedByMyBranch()->sum('grand_total'));

        $inventoryWorth = '₦' . number_format($products->clone()->sum(DB::raw('price * available_quantity')));

        $inventoryCount = number_format($products->clone()->count());

        return [
            Stat::make('Total Sales (Till Date)', $totalSales),
            Stat::make('Inventory Worth', $inventoryWorth),
            Stat::make('Inventory Count', $inventoryCount),
        ];
    }
}
