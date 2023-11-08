<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->paginationPageOptions([5, 10, 25, 50, 100, 200]);
            $table->filters([
                DateRangeFilter::make('created_at')
                    ->label('Select date range')
                    ->maxDate(Carbon::now())
                    ->indicateUsing(function (array $data): ?string {
                        $dates = explode(' - ', $data['created_at'] ?? '');

                        if (! $dates[0] ?? null) {
                            return null;
                        }

                        $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);

                        $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);

                        return 'null' === $dates[0] ? null : 'Created from ' . $startDate->toFormattedDateString() . ' ' . 'Created until ' . $endDate->toFormattedDateString();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        $dates = explode(' - ', $data['created_at'] ?? '');

                        if (! $dates[0] ?? null) {
                            return $query;
                        }

                        $startDate = Carbon::createFromFormat('d/m/Y', $dates[0]);

                        $endDate = Carbon::createFromFormat('d/m/Y', $dates[1]);

                        return $query
                            ->when(
                                'null' === $dates[0] ? null : $startDate,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                'null' === $dates[0] ? null : $endDate,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ]);
        });

        //TextColumn Macros
        TextColumn::macro('formatNaira', fn () => $this->formatStateUsing(fn (string $state) => '₦' . number_format((int) $state, 2)));

        TextColumn::macro('uppercase', fn () => $this->formatStateUsing(fn (string $state) => str($state)->upper()));

        TextColumn::macro('capitalize', fn () => $this->formatStateUsing(fn (string $state) => str($state)->ucfirst()));

        TextColumn::macro('formatTooltip', function () {
            return $this->tooltip(function (TextColumn $column): ?string {
                $state = $column->getState();

                if (mb_strlen((string) $state) <= $column->getCharacterLimit()) {
                    return null;
                }

                return $state;
            });
        });

        //TextEntry Macros
        TextEntry::macro('formatNaira', fn () => $this->formatStateUsing(fn (string $state) => '₦' . number_format((int) $state, 2)));

        TextEntry::macro('uppercase', fn () => $this->formatStateUsing(fn (string $state) => str($state)->upper()));

        TextEntry::macro('capitalize', fn () => $this->formatStateUsing(fn (string $state) => str($state)->ucfirst()));
    }
}
