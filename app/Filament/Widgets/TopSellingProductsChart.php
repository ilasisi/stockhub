<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopSellingProductsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'topSellingProductsChart';

    protected static ?string $heading = 'Top Selling Products (Qty.)';

    protected static ?int $sort = 3;

    protected static ?int $contentHeight = 260;

    protected static ?string $pollingInterval = '3s';

    protected static bool $deferLoading = true;

    public static function canView(): bool
    {
        return true;
    }

    protected function getFilters(): ?array
    {
        return getYears();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $topProducts = Product::ownedByMyBranch()
            ->select('id', 'name', 'items_sold')
            ->whereBetween(
                'created_at',
                [
                    Carbon::create($this->filter, 1, 1)->startOfYear(),
                    Carbon::create($this->filter, 1, 1)->endOfYear(),
                ],
            )
            ->orderByDesc('items_sold')
            ->limit(10)
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 260,
                'stacked' => true,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Purchase',
                    'data' => $topProducts->map(fn ($product) => $product->items_sold),
                ],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
            'legend' => [
                'show' => true,
                'horizontalAlign' => 'right',
                'position' => 'top',
                'markers' => [
                    'height' => 12,
                    'width' => 12,
                    'radius' => 12,
                    'offsetX' => -3,
                    'offsetY' => 2,
                ],
                'itemMargin' => [
                    'vertical' => 10,
                    'horizontal' => 10,
                ],
            ],
            'xaxis' => [
                'categories' => $topProducts->map(fn ($product) => $product->name),
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontWeight' => 400,
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#15803d', '#b45309', '#b91c1c'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'grid' => [
                'show' => true,
            ],
            'markers' => [
                'size' => 0,
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'stroke' => [
                'width' => 0,
            ],
            'colors' => ['#86efac', '#fdba74', '#fca5a5'],
        ];
    }
}
