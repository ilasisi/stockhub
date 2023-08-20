<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
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
